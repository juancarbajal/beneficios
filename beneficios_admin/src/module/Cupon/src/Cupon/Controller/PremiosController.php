<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/08/16
 * Time: 11:45 AM
 */

namespace Cupon\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Cupon\Form\BusquedaCupon;
use Cupon\Form\BusquedaCuponPremios;
use Cupon\Form\FormCuponPremios;
use Cupon\Form\FormEnvioCuponPremios;
use Cupon\Model\CuponPremiosLog;
use Premios\Model\AsignacionPremiosEstadoLog;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class PremiosController extends AbstractActionController
{
    const ESTADO_CUPON_CADUCADO = 'Caducado';
    const ESTADO_CUPON_DESCARGADO = 'Generado';
    const ESTADO_CUPON_REDIMIDO = 'Redimido';
    const ESTADO_CUPON_POR_PAGAR = 'Por Pagar';
    const ESTADO_CUPON_PAGADO = 'Pagado';
    const ESTADO_CUPON_STAND_BY = 'Stand By';
    const ESTADO_CUPON_ANULADO = 'Anulado';
    const OPERACION_REDIMIR = 'Redimir';

    #region ObjectTables
    public function getCuponPremiosTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosTable');
    }

    public function getCuponPremiosLogTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosLogTable');
    }

    public function getOfertaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
    }

    public function getOfertaPremiosSegmentoTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosSegmentoTable');
    }

    public function getOfertaPremiosAtributosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosAtributosTable');
    }

    public function getSegmentoPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\SegmentosPremiosTable');
    }

    public function getCampaniaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosTable');
    }

    public function getConfiguracionesTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\ConfiguracionesTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getUsuarioTable()
    {
        return $this->serviceLocator->get('Usuario\Model\Table\UsuarioTable');
    }

    public function getAsignacionEstadoLogTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosEstadoLogTable');
    }
    #endregion

    #region Inicializando Data
    public function inicializacionBusqueda()
    {
        $dataEmpCli = array();
        $filterEmpCli = array();

        try {
            foreach ($this->getAsignacionTable()->getEmpresasAsignacion() as $empresa) {
                $dataEmpCli[$empresa->id] = $empresa->Empresa;
                $filterEmpCli[$empresa->id] = [$empresa->id];
            }

        } catch (\Exception $ex) {
            $dataEmpCli = array();
        }

        $formulario['prov'] = $dataEmpCli;
        $filtro['prov'] = array_keys($filterEmpCli);
        return array($formulario, $filtro);
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $opcion = null;
        $nombreEmpresa = null;

        $busqueda = array(
            'Empresa' => 'Empresa',
            'Campania' => 'Campania',
            'Oferta' => 'Titulo',
            'Cupon' => 'CodigoCupon',
            'PVP' => 'PrecioVentaPublico',
            'PB' => 'PrecioBeneficio',
            'Estado' => 'EstadoCupon',
            'UltimaActualizacion' => 'UltimaActualizacion',
        );

        if ($this->identity()->TipoUsuario == "proveedor") {
            return $this->redirect()->toRoute('cupon-premios', array('action' => 'edit'));
        }

        $data = $this->inicializacionBusqueda();
        $empresaUsuario = $this->identity()->BNF_Empresa_id;
        if (!empty($empresaUsuario)) {
            $nombreEmpresa = $this->getEmpresaTable()->getEmpresa($empresaUsuario)->NombreComercial;
        }
        $formSearch = new BusquedaCuponPremios("busqueda", $data[0], $empresaUsuario);
        $formEnviar = new FormEnvioCuponPremios();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = !empty($request->getPost()->Empresa) ? $request->getPost()->Empresa : null;
            $campania = !empty($request->getPost()->Campania) ? $request->getPost()->Campania : null;
            $oferta = !empty($request->getPost()->Oferta) ? $request->getPost()->Oferta : null;
            $estado = !empty($request->getPost()->EstadoCupon) ? $request->getPost()->EstadoCupon : null;
            $desde = !empty($request->getPost()->FechaInicio) ? $request->getPost()->FechaInicio : null;
            $hasta = !empty($request->getPost()->FechaFin) ? $request->getPost()->FechaFin : null;
            $codigo = !empty($request->getPost()->Cupon) ? $request->getPost()->Cupon : null;
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $campania = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $oferta = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
            $estado = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
            $desde = $this->params()->fromRoute('q5') ? $this->params()->fromRoute('q5') : null;
            $hasta = $this->params()->fromRoute('q6') ? $this->params()->fromRoute('q6') : null;
            $codigo = $this->params()->fromRoute('q7') ? $this->params()->fromRoute('q7') : null;
            $estado = str_replace("-", " ", $estado);
        }

        $formSearch->setData(
            array(
                "Empresa" => isset($empresaUsuario) ? $empresaUsuario : $empresa,
                "Campania" => $campania,
                "Oferta" => $oferta,
                "EstadoCupon" => $estado,
                "FechaInicio" => $desde,
                "FechaFin" => $hasta,
                "Cupon" => $codigo
            )
        );

        if (!empty($estado) && $estado == "Redimido" || $estado == "Por Pagar" || $estado == "Pagado") {
            $opcion = $estado;
        }

        //Determinar ordenamiento
        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];
        } else {
            $order_by_o = 'id';
            $order_by = 'id';
        }

        if (!$desde) {
            $desde = date("1990-01-01");
        }

        if (!$hasta) {
            $hasta = date("Y-m-d");
        }

        //Se obtiene los datos filtrados y la paginacion segun el orden
        $paginator = null;
        if (!empty($estado)) {
            $asignaciones = $this->getCuponPremiosTable()
                ->getListaCuponesPremios($order_by, $order, $empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo);
            $paginator = new Paginator(new paginatorIterator($asignaciones, $order_by));
            $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);
        }

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'cuponpremios' => 'active',
                'listCuponpremios' => 'active',
                'asignaciones' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'formS' => $formSearch,
                'formE' => $formEnviar,
                'p' => $page,
                'q1' => $empresa,
                'q2' => $campania,
                'q3' => $oferta,
                'q4' => $estado,
                'q5' => $desde,
                'q6' => $hasta,
                'q7' => $codigo,
                'opcion' => $opcion,
                'nombreEmpresa' => $nombreEmpresa,
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $dataIdCupon = (int)$this->params()->fromRoute('id', 0);

        $mensaje = null;
        $action = null;
        $idCupon = null;
        $formName = null;
        $fomData = array();
        $formS = new BusquedaCupon();

        $tipo = $this->identity()->TipoUsuario;
        if ($tipo == 'proveedor') {
            $formName = "addFormCupon";
        } else {
            $formName = "changeCupon";
        }

        $empresa = $this->identity()->BNF_Empresa_id;
        $active = (empty($empresa)) ? true : false;
        $formC = new FormCuponPremios($formName, $empresa);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $codigoCupon = $request->getPost()->cupon ? $request->getPost()->cupon : null;
            if ($codigoCupon != null) {
                $cupon = $this->getCuponPremiosTable()
                    ->searchCuponPremios($codigoCupon, $this->identity()->BNF_Empresa_id);
                if (is_object($cupon)) {
                    $oferta = $this->getOfertaPremiosTable()->getOfertaPremios($cupon->BNF3_Oferta_Premios_id);
                    $empresaProv = $this->getEmpresaTable()->getEmpresa($oferta->BNF_Empresa_id)->NombreComercial;
                    $cuponLog = $this->getCuponPremiosLogTable()->getCuponPremiosLogByEstado($cupon->id, $cupon->EstadoCupon);

                    if ($oferta->TipoPrecio == "Unico") {
                        $precioCupon = $oferta->PrecioVentaPublico;
                        $precioBeneficio = $oferta->PrecioBeneficio;
                    } else {
                        $ofertaAtributo = $this->getOfertaPremiosAtributosTable()
                            ->getOfertaPremiosAtributos($cupon->BNF3_Oferta_Premios_Atributos_id);
                        $precioCupon = $ofertaAtributo->PrecioVentaPublico;
                        $precioBeneficio = $ofertaAtributo->PrecioBeneficio;
                        $oferta->Titulo = $ofertaAtributo->NombreAtributo;
                    }

                    $ofertaSegmento = $this->getOfertaPremiosSegmentoTable()
                        ->getOfertaPremiosSegmentoByOfertaAll($oferta->id);

                    $campaniaId = 0;
                    foreach ($ofertaSegmento as $value) {
                        $segmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($value->BNF3_Segmento_id);
                        $campaniaId = $segmento->BNF3_Campania_id;
                        break;
                    }

                    $campania = $this->getCampaniaPremiosTable()->getCampaniasP($campaniaId);

                    $fomData['id'] = $cupon->id;
                    $fomData['CodigoCupon'] = $codigoCupon;
                    $fomData['Titulo'] = $oferta->Titulo;
                    $fomData['EmpresaProv'] = $empresaProv;
                    $fomData['Campania'] = $campania->NombreCampania;
                    $fomData['EstadoCampania'] = ($campania->EstadoCampania == 'Caducado') ? 'Caducada' : 'Activo';
                    $fomData['EstadoCupon'] = $cupon->EstadoCupon;
                    $fomData['PrecioCupon'] = $precioCupon;
                    $fomData['PrecioBeneficio'] = $precioBeneficio;
                    $fomData['PremiosUtilizados'] = $cupon->PremiosUtilizados;
                    $fomData['PrecioFinal'] = $precioCupon - $cupon->PremiosUtilizados;
                    $fomData['CondicionesUso'] = $oferta->CondicionesUso;
                    $fomData['FechaFinVigencia'] = date_format(date_create($cupon->FechaVigencia), 'Y-m-d');
                    $fomData['Comentarios'] = !empty($cuponLog->Comentario) ? $cuponLog->Comentario : "";
                    $formC->setData($fomData);

                    $idCupon = $cupon->id;
                } else {
                    $mensaje = "Código inválido.";
                }
                $formS->setData(array('cupon' => $codigoCupon));
            }
        } elseif (!empty($dataIdCupon)) {
            $dataCupon = $this->getCuponPremiosTable()->getCuponPremios($dataIdCupon);
            $cupon = $this->getCuponPremiosTable()
                ->searchCuponPremios($dataCupon->CodigoCupon, $this->identity()->BNF_Empresa_id);
            if (is_object($cupon)) {
                $oferta = $this->getOfertaPremiosTable()->getOfertaPremios($cupon->BNF3_Oferta_Premios_id);
                $empresaProv = $this->getEmpresaTable()->getEmpresa($oferta->BNF_Empresa_id)->NombreComercial;
                $cuponLog = $this->getCuponPremiosLogTable()->getCuponPremiosLogByEstado($cupon->id, $cupon->EstadoCupon);

                if ($oferta->TipoPrecio == "Unico") {
                    $precioCupon = $oferta->PrecioVentaPublico;
                    $precioBeneficio = $oferta->PrecioBeneficio;
                } else {
                    $ofertaAtributo = $this->getOfertaPremiosAtributosTable()
                        ->getOfertaPremiosAtributos($cupon->BNF3_Oferta_Premios_Atributos_id);
                    $precioCupon = $ofertaAtributo->PrecioVentaPublico;
                    $precioBeneficio = $ofertaAtributo->PrecioBeneficio;
                    $oferta->Titulo = $ofertaAtributo->NombreAtributo;
                }

                $ofertaSegmento = $this->getOfertaPremiosSegmentoTable()
                    ->getOfertaPremiosSegmentoByOfertaAll($oferta->id);

                $campaniaId = 0;
                foreach ($ofertaSegmento as $value) {
                    $segmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($value->BNF3_Segmento_id);
                    $campaniaId = $segmento->BNF3_Campania_id;
                    break;
                }

                $campania = $this->getCampaniaPremiosTable()->getCampaniasP($campaniaId);

                $fomData['id'] = $cupon->id;
                $fomData['CodigoCupon'] = $dataCupon->CodigoCupon;
                $fomData['Titulo'] = $oferta->Titulo;
                $fomData['EmpresaProv'] = $empresaProv;
                $fomData['Campania'] = $campania->NombreCampania;
                $fomData['EstadoCampania'] = ($campania->EstadoCampania == 'Caducado') ? 'Caducada' : 'Activo';
                $fomData['EstadoCupon'] = $cupon->EstadoCupon;
                $fomData['PrecioCupon'] = $precioCupon;
                $fomData['PrecioBeneficio'] = $precioBeneficio;
                $fomData['PremiosUtilizados'] = $cupon->PremiosUtilizados;
                $fomData['PrecioFinal'] = $precioCupon - $cupon->PremiosUtilizados;
                $fomData['CondicionesUso'] = $oferta->CondicionesUso;
                $fomData['FechaFinVigencia'] = date_format(date_create($cupon->FechaVigencia), 'Y-m-d');
                $fomData['Comentarios'] = !empty($cuponLog->Comentario) ? $cuponLog->Comentario : "";
                $formC->setData($fomData);

                $idCupon = $cupon->id;
            } else {
                $mensaje = "Código inválido.";
            }
            $formS->setData(array('cupon' => $dataCupon->CodigoCupon));
        }

        $tipo = $this->identity()->TipoUsuario;
        if ($tipo == 'proveedor') {
            $action = "add";
            $formC->get('submit')->setAttribute('value', 'Redimir');
        } else {
            $action = "change";
            $formC->get('submit')->setAttribute('value', 'Grabar');
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'cuponPremios' => 'active',
                'editCuponPremios' => 'active',
                'id' => $dataIdCupon,
                'cupon' => $idCupon,
                'action' => $action,
                'tipoUsuario' => $tipo,
                'mensaje' => $mensaje,
                'formS' => $formS,
                'form' => $formC,
                'active' => $active,
            )
        );
    }

    public function addAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $dias = $this->getConfiguracionesTable()->getConfig('dias_expiracion')->Atributo;
        $request = $this->getRequest();
        $response = $this->getResponse();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $comentario = (!empty(trim($post_data['Comentarios'])))
                ? $post_data['Comentarios'] : "redimido por el proveedor";
            $cupon = $this->getCuponPremiosTable()->getCuponPremios($id);
            if (!is_object($cupon)) {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'condition' => false,
                            'message' => 'El cupón no existe.'
                        )
                    )
                );
            } else {
                if ($cupon->EstadoCupon == $this::ESTADO_CUPON_REDIMIDO) {
                    $dataLog = $this->getCuponPremiosLogTable()->getCuponPremiosLogByCuponId($cupon->id);
                    $dataUsuario = $this->getUsuarioTable()->getUsuario($dataLog->BNF_Usuario_id);

                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'condition' => false,
                                'tittle' => 'Lo sentimos',
                                'message' => 'El cupón ya fue utilizado en la fecha ' .
                                    date_format(date_create($cupon->FechaRedimido), 'Y-m-d') . " por el usuario " .
                                    $dataUsuario->NumeroDocumento . " - " .
                                    $dataUsuario->Nombres . " " .
                                    $dataUsuario->Apellidos
                            )
                        )
                    );
                } elseif ($cupon->EstadoCupon == $this::ESTADO_CUPON_CADUCADO) {
                    $oferta = $this->getOfertaPremiosTable()->getOfertaPremios($cupon->BNF3_Oferta_Premios_id);

                    if ($oferta->TipoPrecio == "Unico") {
                        $fecha = $oferta->FechaVigencia;
                    } else {
                        $ofertaAtributo = $this->getOfertaPremiosAtributosTable()
                            ->getAllOfertaPremiosAtributos($cupon->BNF3_Oferta_Premios_Atributos_id);
                        $fecha = $ofertaAtributo->FechaVigencia;
                    }

                    $hoy = date_create('now');
                    $vigencia = date_create($fecha);
                    date_add($vigencia, date_interval_create_from_date_string($dias . ' days'));
                    $diferencia = date_diff($hoy, $vigencia);

                    if ($diferencia->format("%r%a") >= 0) {
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => false,
                                    'condition' => true,
                                    'message' => 'El cupón expiró el día ' .
                                        date_format(date_create($cupon->FechaCaducado), 'Y-m-d') .
                                        ". Desea redimir?"
                                )
                            )
                        );
                    } else {
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => false,
                                    'condition' => false,
                                    'tittle' => 'Lo sentimos',
                                    'message' => 'El cupón expiró el día ' .
                                        date_format(date_create($cupon->FechaCaducado), 'Y-m-d') .
                                        ". No se puede redimir porque paso el periodo de gracia"
                                )
                            )
                        );
                    }
                } elseif ($cupon->EstadoCupon == $this::ESTADO_CUPON_DESCARGADO) {
                    $oferta = $this->getOfertaPremiosTable()->getOfertaPremios($cupon->BNF3_Oferta_Premios_id);

                    if ($oferta->TipoPrecio == "Unico") {
                        $fecha = $oferta->FechaVigencia;
                    } else {
                        $ofertaAtributo = $this->getOfertaPremiosAtributosTable()
                            ->getOfertaPremiosAtributos($cupon->BNF3_Oferta_Premios_Atributos_id);
                        $fecha = $ofertaAtributo->FechaVigencia;
                    }

                    $hoy = date_create('now');
                    $vigencia = date_create($fecha);
                    date_add($vigencia, date_interval_create_from_date_string($dias . ' days'));
                    $diferencia = date_diff($hoy, $vigencia);

                    if ($diferencia->format("%r%a") >= 0) {

                        $asignacion = $this->getAsignacionTable()->getAsignacion($cupon->BNF3_Asignacion_Premios_id);

                        $asignacionEstadoLog = new AsignacionPremiosEstadoLog();
                        $asignacionEstadoLog->BNF3_Asignacion_Premios_id = $asignacion->id;
                        $asignacionEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $asignacionEstadoLog->CantidadPremios = (int)$asignacion->CantidadPremios;
                        $asignacionEstadoLog->CantidadPremiosUsados = (int)$asignacion->CantidadPremiosUsados;
                        $asignacionEstadoLog->CantidadPremiosDisponibles = (int)$asignacion->CantidadPremiosDisponibles;
                        $asignacionEstadoLog->CantidadPremiosEliminados = (int)$asignacion->CantidadPremiosEliminados;
                        $asignacionEstadoLog->EstadoPremios = $asignacion->EstadoPremios;
                        $asignacionEstadoLog->Operacion = $this::OPERACION_REDIMIR;
                        $asignacionEstadoLog->Premios = $cupon->PremiosUtilizados;
                        $asignacionEstadoLog->Motivo = "Redimir Premios";
                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionEstadoLogTable()->saveAsignacionPremiosEstadoLog($asignacionEstadoLog);

                        $this->getCuponPremiosTable()->redimirCuponPremios($cupon->id);

                        $cuponPremiosLog = new CuponPremiosLog();
                        $cuponPremiosLog->BNF3_Cupon_Premios_id = $cupon->id;
                        $cuponPremiosLog->CodigoCupon = $cupon->CodigoCupon;
                        $cuponPremiosLog->EstadoCupon = "Redimido";
                        $cuponPremiosLog->BNF3_Oferta_Premios_id = $cupon->BNF3_Oferta_Premios_id;
                        $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $cupon->BNF3_Oferta_Premios_Atributos_id;
                        $cuponPremiosLog->BNF_Cliente_id = $cupon->BNF_Cliente_id;
                        $cuponPremiosLog->BNF_Usuario_id = $this->identity()->id;
                        $cuponPremiosLog->Comentario = $comentario;
                        $this->getCuponPremiosLogTable()->saveCuponPremiosLog($cuponPremiosLog);

                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => true,
                                    'condition' => false,
                                    'tittle' => 'Operación Completada',
                                    'message' => 'El cupón fue redimido correctamente.'
                                )
                            )
                        );
                    } else {
                        $cupon->EstadoCupon = 'Caducado';
                        $cupon->FechaCaducado = $fecha;
                        $this->getCuponPremiosTable()->saveCuponPremios($cupon);
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => false,
                                    'condition' => false,
                                    'tittle' => 'Lo sentimos',
                                    'message' => 'El cupón expiró el día ' .
                                        date_format(date_create($cupon->FechaCaducado), 'Y-m-d') .
                                        ". No se puede redimir porque paso el periodo de gracia"
                                )
                            )
                        );
                    }
                } else {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'condition' => false,
                                'tittle' => 'Lo sentimos',
                                'message' => 'El cupón está ' . $cupon->EstadoCupon
                            )
                        )
                    );
                }
            }
        }
        return $response;
    }

    public function redimirAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $comentario = (!empty(trim($post_data['Comentarios'])))
                ? $post_data['Comentarios'] : "redimido por el proveedor";
            $csrf = new Csrf();
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    $cupon = $this->getCuponPremiosTable()->getCuponPremios($id);
                    if ($cupon == false) {
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => false,
                                    'tittle' => 'Error',
                                    'message' => 'El cupon no existe.'
                                )
                            )
                        );
                    } else {
                        $asignacion = $this->getAsignacionTable()->getAsignacion($cupon->BNF3_Asignacion_Premios_id);

                        $asignacionEstadoLog = new AsignacionPremiosEstadoLog();
                        $asignacionEstadoLog->BNF3_Asignacion_Premios_id = $asignacion->id;
                        $asignacionEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $asignacionEstadoLog->CantidadPremios = (int)$asignacion->CantidadPremios;
                        $asignacionEstadoLog->CantidadPremiosUsados = (int)$asignacion->CantidadPremiosUsados;
                        $asignacionEstadoLog->CantidadPremiosDisponibles = (int)$asignacion->CantidadPremiosDisponibles;
                        $asignacionEstadoLog->CantidadPremiosEliminados = (int)$asignacion->CantidadPremiosEliminados;
                        $asignacionEstadoLog->EstadoPremios = $asignacion->EstadoPremios;
                        $asignacionEstadoLog->Operacion = $this::OPERACION_REDIMIR;
                        $asignacionEstadoLog->Premios = $cupon->PremiosUtilizados;
                        $asignacionEstadoLog->Motivo = "Redimir Premios";
                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionEstadoLogTable()->saveAsignacionPremiosEstadoLog($asignacionEstadoLog);

                        $this->getCuponPremiosTable()->redimirCuponPremios($cupon->id);

                        $cuponPremiosLog = new CuponPremiosLog();
                        $cuponPremiosLog->BNF3_Cupon_Premios_id = $cupon->id;
                        $cuponPremiosLog->CodigoCupon = $cupon->CodigoCupon;
                        $cuponPremiosLog->EstadoCupon = "Redimido";
                        $cuponPremiosLog->BNF3_Oferta_Premios_id = $cupon->BNF3_Oferta_Premios_id;
                        $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $cupon->BNF3_Oferta_Premios_Atributos_id;
                        $cuponPremiosLog->BNF_Cliente_id = $cupon->BNF_Cliente_id;
                        $cuponPremiosLog->BNF_Usuario_id = $this->identity()->id;
                        $cuponPremiosLog->Comentario = $comentario;
                        $this->getCuponPremiosLogTable()->saveCuponPremiosLog($cuponPremiosLog);

                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => true,
                                    'tittle' => 'Operación Completada',
                                    'message' => 'El cupon fue redimido correctamente.'
                                )
                            )
                        );
                    }
                } else {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'tittle' => 'Error',
                                'message' => 'El cupon no existe.'
                            )
                        )
                    );
                }
            } else {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'tittle' => 'Error',
                            'message' => 'El cupon no existe.'
                        )
                    )
                );
            }
        }
        return $response;
    }

    public function changeAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $comentario = $post_data['Comentarios'];
            $estado = $post_data['EstadoCupon'];
            $csrf = new Csrf();
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    $cuponPremios = $this->getCuponPremiosTable()->getCuponPremios($id);
                    if ($estado == $this::ESTADO_CUPON_DESCARGADO) {
                        $this->getCuponPremiosTable()->generadoCuponPremios($cuponPremios->id);
                    } elseif ($estado == $this::ESTADO_CUPON_REDIMIDO) {
                        $this->getCuponPremiosTable()->redimirCuponPremios($cuponPremios->id);

                        $asignacion = $this->getAsignacionTable()->getAsignacion($cuponPremios->BNF3_Asignacion_Premios_id);

                        $asignacionEstadoLog = new AsignacionPremiosEstadoLog();
                        $asignacionEstadoLog->BNF3_Asignacion_Premios_id = $asignacion->id;
                        $asignacionEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $asignacionEstadoLog->CantidadPremios = (int)$asignacion->CantidadPremios;
                        $asignacionEstadoLog->CantidadPremiosUsados = (int)$asignacion->CantidadPremiosUsados;
                        $asignacionEstadoLog->CantidadPremiosDisponibles = (int)$asignacion->CantidadPremiosDisponibles;
                        $asignacionEstadoLog->CantidadPremiosEliminados = (int)$asignacion->CantidadPremiosEliminados;
                        $asignacionEstadoLog->EstadoPremios = $asignacion->EstadoPremios;
                        $asignacionEstadoLog->Operacion = $this::OPERACION_REDIMIR;
                        $asignacionEstadoLog->Premios = $cuponPremios->PremiosUtilizados;
                        $asignacionEstadoLog->Motivo = "Redimir Premios";
                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionEstadoLogTable()->saveAsignacionPremiosEstadoLog($asignacionEstadoLog);
                    } elseif ($estado == $this::ESTADO_CUPON_POR_PAGAR) {
                        $this->getCuponPremiosTable()->porPagarCuponPremios($cuponPremios->id);
                    } elseif ($estado == $this::ESTADO_CUPON_PAGADO) {
                        $this->getCuponPremiosTable()->pagadoCuponPremios($cuponPremios->id);
                    } elseif ($estado == $this::ESTADO_CUPON_STAND_BY) {
                        $this->getCuponPremiosTable()->standByCuponPremios($cuponPremios->id);
                    } elseif ($estado == $this::ESTADO_CUPON_ANULADO) {
                        $this->getCuponPremiosTable()->anularCuponPremios($cuponPremios->id);
                    } elseif ($estado == $this::ESTADO_CUPON_CADUCADO) {
                        $this->getCuponPremiosTable()->caducadoCuponPremios($cuponPremios->id);
                    }


                    $cuponPremiosLog = new CuponPremiosLog();
                    $cuponPremiosLog->BNF3_Cupon_Premios_id = $cuponPremios->id;
                    $cuponPremiosLog->CodigoCupon = $cuponPremios->CodigoCupon;
                    $cuponPremiosLog->EstadoCupon = $estado;
                    $cuponPremiosLog->BNF3_Oferta_Premios_id = $cuponPremios->BNF3_Oferta_Premios_id;
                    $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $cuponPremios->BNF3_Oferta_Premios_Atributos_id;
                    $cuponPremiosLog->BNF_Cliente_id = $cuponPremios->BNF_Cliente_id;
                    $cuponPremiosLog->BNF_Usuario_id = $this->identity()->id;
                    $cuponPremiosLog->Comentario = $comentario;
                    $this->getCuponPremiosLogTable()->saveCuponPremiosLog($cuponPremiosLog);

                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => true,
                                'tittle' => 'Operación Completada',
                                'message' => 'El cupon fue procesado correctamente.'
                            )
                        )
                    );
                } else {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'tittle' => 'Error',
                                'message' => 'El cupon no existe.'
                            )
                        )
                    );
                }
            } else {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'tittle' => 'Error',
                            'message' => 'Error al enviar los datos.'
                        )
                    )
                );
            }
        }
        return $response;
    }

    public function envioCuponAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $por_pagar_check = $post_data['por_pagar_check'];
            $pagado_check = $post_data['pagado_check'];
            $csrf = new Csrf();
            if (isset($post_data['csrf'])) {
                if ($csrf->verifyToken($post_data['csrf'])) {
                    $comentario = "modificación a través del listado";
                    if (count($por_pagar_check) > 0) {
                        foreach ($por_pagar_check as $value) {
                            $cuponPremios = $this->getCuponPremiosTable()->getCuponPremios($value);
                            $this->getCuponPremiosTable()->porPagarCuponPremios($cuponPremios->id);

                            $cuponPremiosLog = new CuponPremiosLog();
                            $cuponPremiosLog->BNF3_Cupon_Premios_id = $cuponPremios->id;
                            $cuponPremiosLog->CodigoCupon = $cuponPremios->CodigoCupon;
                            $cuponPremiosLog->EstadoCupon = $this::ESTADO_CUPON_POR_PAGAR;
                            $cuponPremiosLog->BNF3_Oferta_Premios_id = $cuponPremios->BNF3_Oferta_Premios_id;
                            $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $cuponPremios->BNF3_Oferta_Premios_Atributos_id;
                            $cuponPremiosLog->BNF_Cliente_id = $cuponPremios->BNF_Cliente_id;
                            $cuponPremiosLog->BNF_Usuario_id = $this->identity()->id;
                            $cuponPremiosLog->Comentario = $comentario;
                            $this->getCuponPremiosLogTable()->saveCuponPremiosLog($cuponPremiosLog);
                        }
                    } elseif (count($pagado_check) > 0) {
                        foreach ($pagado_check as $value) {
                            $cuponPremios = $this->getCuponPremiosTable()->getCuponPremios($value);
                            $this->getCuponPremiosTable()->pagadoCuponPremios($cuponPremios->id);

                            $cuponPremiosLog = new CuponPremiosLog();
                            $cuponPremiosLog->BNF3_Cupon_Premios_id = $cuponPremios->id;
                            $cuponPremiosLog->CodigoCupon = $cuponPremios->CodigoCupon;
                            $cuponPremiosLog->EstadoCupon = $this::ESTADO_CUPON_PAGADO;
                            $cuponPremiosLog->BNF3_Oferta_Premios_id = $cuponPremios->BNF3_Oferta_Premios_id;
                            $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id = $cuponPremios->BNF3_Oferta_Premios_Atributos_id;
                            $cuponPremiosLog->BNF_Cliente_id = $cuponPremios->BNF_Cliente_id;
                            $cuponPremiosLog->BNF_Usuario_id = $this->identity()->id;
                            $cuponPremiosLog->Comentario = $comentario;
                            $this->getCuponPremiosLogTable()->saveCuponPremiosLog($cuponPremiosLog);
                        }
                    }

                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => true,
                                'direccion' => '/cupon-premios',
                                'tittle' => 'Operación Completada',
                                'message' => 'El cupon fue procesado correctamente.'
                            )
                        )
                    );
                } else {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'tittle' => 'Error',
                                'message' => 'El cupon no existe.'
                            )
                        )
                    );
                }
            }
        }
        return $response;
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresaUsuario = $this->identity()->BNF_Empresa_id;
        $empresa = (!empty($empresaUsuario)) ? $empresaUsuario : (int)$this->params()->fromRoute('empresa', 0);
        $campania = (int)$this->params()->fromRoute('campania', 0);
        $oferta = (int)$this->params()->fromRoute('oferta', 0);
        $estado = $this->params()->fromRoute('estado', null);
        $desde = $this->params()->fromRoute('desde', null);
        $hasta = $this->params()->fromRoute('hasta', null);
        $codigo = $this->params()->fromRoute('codigo', null);

        if ($estado == "Por-Pagar") {
            $estado = "Por Pagar";
        } elseif ($estado == "Stand-By") {
            $estado = "Stand By";
        }

        $resultado = $this->getCuponPremiosTable()
            ->reporteCuponPremios($empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Información del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Cupones Premios")
                ->setSubject("Cupones Premios")
                ->setDescription("Documento Lista de Cupones Premios")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Cupones Premios");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:I' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $styleArray2 = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                ),
            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:I' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Empresa Proveedora')
                ->setCellValue('C1', 'Campaña')
                ->setCellValue('D1', 'Oferta')
                ->setCellValue('E1', 'Código Cupon')
                ->setCellValue('F1', 'PVP')
                ->setCellValue('G1', 'PB')
                ->setCellValue('H1', 'Estado')
                ->setCellValue('I1', 'Ultima Actualización');
            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Empresa)
                    ->setCellValue('C' . $i, $registro->Campania)
                    ->setCellValue('D' . $i, $registro->Oferta)
                    ->setCellValue('E' . $i, $registro->CodigoCupon)
                    ->setCellValue('F' . $i, $registro->PrecioVentaPublico)
                    ->setCellValue('G' . $i, $registro->PrecioBeneficio)
                    ->setCellValue('H' . $i, $registro->EstadoCupon)
                    ->setCellValue('I' . $i, $registro->UltimaActualizacion);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="CuponesPremios.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function getDataEmpresaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $dataCampanias = array();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    $campanias = $this->getCampaniaPremiosTable()->getCampaniasPByEmpresa($id);
                    foreach ($campanias as $value) {
                        $dataCampanias[] = array('id' => $value->id, 'text' => $value->NombreCampania);
                    }

                    $state = true;
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'campanias' => $dataCampanias,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function getDataCampaniaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $dataOfertas = array();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    $ofertas = $this->getOfertaPremiosTable()->getAllOfertaPremiosByCampania($id);
                    foreach ($ofertas as $value) {
                        $dataOfertas[] = array('id' => $value->id, 'text' => $value->Titulo);
                    }

                    $state = true;
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'ofertas' => $dataOfertas,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }
}
