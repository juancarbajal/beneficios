<?php
/**
 * Created by PhpStorm.
 * User: janaq
 * Date: 17/06/16
 * Time: 11:39 AM
 */

namespace Premios\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Premios\Form\BuscarCampaniasPremiosForm;
use Premios\Form\FormCampaniasPR;
use Premios\Form\FormSegmentoP;
use Premios\Model\AsignacionPremiosEstadoLog;
use Premios\Model\CampaniaPremiosLog;
use Premios\Model\CampaniasPremios;
use Premios\Model\CampaniasPremiosEmpresas;
use Premios\Model\Filter\CampaniasPremiosFilter;
use Premios\Model\Filter\SegmentosPremiosFilter;
use Premios\Model\SegmentosPremios;
use Premios\Model\SegmentosPremiosLog;
use Zend\I18n\Validator\IsInt;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class CampaniasController extends AbstractActionController
{
    const SEGMENT_TYPE_ONE = "Clasico";
    const SEGMENT_TYPE_TWO = "Personalizado";

    const MESSAGE_ERROR = "No se Registró, revisar los datos ingresados.";
    const MESSAGE_SAVE = "Campaña Registrada.";
    const MESSAGE_UPDATE = "Campaña Actualizada.";

    const OPERACION_CANCELAR = "Cancelar";
    const OPERACION_ASIGNAR = "Asignar";

    #region ObjectTables
    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosTable');
    }

    public function getSegmetosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\SegmentosPremiosTable');
    }

    public function getCampaniaEmpresaTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosEmpresasTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosTable');
    }

    public function getOfertaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
    }

    public function getCampaniaLogTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniaPremiosLogTable');
    }

    public function getSegmentoLogTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\SegmentosPremiosLogTable');
    }

    public function getCuponPremiosTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosTable');
    }

    public function getOfertaPremiosAtributosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosAtributosTable');
    }

    public function getOfertaPremiosSegmentoTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosSegmentoTable');
    }

    public function getAsignacionPremiosEstadoLogTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosEstadoLogTable');
    }
    #endregion

    #region Inicializando Data
    public function inicializacionBusqueda()
    {
        $comboemp = array();
        $combofemp = array();

        try {
            foreach ($this->getCampaniaTable()->getEmpresasCliente("busqueda") as $empresa) {
                $comboemp[$empresa->id] = $empresa->Empresa;
                $combofemp[$empresa->id] = [$empresa->id];
            }
        } catch (\Exception $ex) {
            $comboemp = array();
        }

        $formulario['emp'] = $comboemp;
        $filtro['emp'] = array_keys($combofemp);

        return array($formulario, $filtro);
    }

    public function inicializacion()
    {
        $dataEmpCli = array();
        $filterEmpCli = array();

        try {
            foreach ($this->getEmpresaTable()->getEmpresasCliente() as $empresa) {
                $dataEmpCli[$empresa->id] = $empresa->NombreComercial . ' - ' . $empresa->RazonSocial .
                    ' - ' . $empresa->Ruc;
                $filterEmpCli[$empresa->id] = [$empresa->id];
            }
        } catch (\Exception $ex) {
            $dataEmpCli = array();
        }

        $formulario['emp'] = $dataEmpCli;
        $filtro['emp'] = array_keys($filterEmpCli);

        return array($formulario, $filtro);
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $fecha = null;
        $campania = null;
        $presupuesto = null;
        $inicio = null;
        $final = null;
        $activo = null;

        $busqueda = array(
            'Campania' => 'NombreCampania',
            'Presupuesto' => 'Presupuesto',
            'Inicio' => 'VigenciaInicio',
            'Fin' => 'VigenciaFin',
            'Empresa' => 'NombreComercial',
            'TipoSegmento' => 'TipoSegmento',
            'Estado' => 'EstadoCampania'
        );

        $data = $this->inicializacionBusqueda();
        $form = new BuscarCampaniasPremiosForm('buscar empresas', $data[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = $request->getPost()->Empresas ? $request->getPost()->Empresas : null;
            $fecha = $request->getPost()->FechaCampania ? $request->getPost()->FechaCampania : null;
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $fecha = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
        }
        $form->setData(array("Empresas" => $empresa, "FechaCampania" => $fecha));

        //Determinar ordenamiento
        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'FechaCreacion';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];
        } else {
            $order_by_o = 'id';
            $order_by = 'BNF3_Campanias.FechaCreacion';
        }

        //Se obtiene los datos filtrados y la paginacion segun el orden
        $campanias = $this->getCampaniaTable()->getListaDetallesCampania($order_by, $order, $empresa, $fecha);
        $paginator = new Paginator(new paginatorIterator($campanias, $order_by));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'premios_camp' => 'active',
                'camptos' => 'active',
                'campanias' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'form' => $form,
                'p' => $page,
                'q1' => $empresa,
                'q2' => $fecha,
            )
        );
    }

    public function addAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $confirm = null;
        $type = null;
        $errorPersonalized = null;
        $personalizedSub = null;

        $typeSeg = "";
        $totalSeg = 0;

        $classicSeg = array();
        $classicPtos = array();
        $classicPers = array();
        $classicComment = array();
        $classicSegMessage = array();
        $classicPtosMessage = array();
        $classicPersMessage = array();
        $classicCommentMessage = array();

        $datos = $this->inicializacion();
        $form = new FormCampaniasPR('registrar', $datos[0]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $validate = new CampaniasPremiosFilter();
            $form->setInputFilter($validate->getInputFilter($datos[1]));
            $form->setData($request->getPost());

            $approved = false;
            $message = array();
            if ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_ONE) {
                //Validación de Campos
                $resultados = $this->validarCampos($request);
                $approved = $resultados[0];
                $message = $resultados[1];
            } elseif ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_TWO) {
                $approved = true;
            }

            if ($form->isValid() and $approved) {
                //Agregar CampaniasPremios
                $campaniaPremios = new CampaniasPremios();
                $campaniaPremios->NombreCampania = trim($request->getPost()->NombreCampania);
                $campaniaPremios->TipoSegmento = $request->getPost()->TipoSegmento;
                $campaniaPremios->FechaCampania = $request->getPost()->FechaCampania;
                $campaniaPremios->VigenciaInicio = $request->getPost()->VigenciaInicio;
                $campaniaPremios->VigenciaFin = $request->getPost()->VigenciaFin;
                $campaniaPremios->PresupuestoNegociado = (int)$request->getPost()->PresupuestoNegociado;
                $campaniaPremios->ParametroAlerta = (int)$request->getPost()->ParametroAlerta;
                $campaniaPremios->Comentario = $request->getPost()->Comentario;
                $campaniaPremios->Relacionado = (int)$request->getPost()->Relacionado;
                $campaniaPremios->EstadoCampania = $request->getPost()->EstadoCampania;
                $campaniaPremios->Eliminado = 0;
                $campaniaPremios->id = $this->getCampaniaTable()->saveCampaniasP($campaniaPremios);

                //Agregando Relacion CampaniaP Empresa
                $campaniaEmpresa = new CampaniasPremiosEmpresas();
                $campaniaEmpresa->BNF3_Campania_id = $campaniaPremios->id;
                $campaniaEmpresa->BNF_Empresa_id = $request->getPost()->Empresa;
                $campaniaEmpresa->Eliminado = 0;
                $id = $this->getCampaniaEmpresaTable()->saveCampaniasPremiosEmpresas($campaniaEmpresa);

                $segmentos = "";
                $contador = 0;
                //Agregando Segmentos
                if ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_ONE) {
                    $classicPtos = $request->getPost()->classicPtos;
                    $classicPers = $request->getPost()->classicPers;
                    $classicComment = $request->getPost()->classicComment;
                    foreach ($request->getPost()->classicSeg as $key => $value) {
                        $segmentoPremios = new SegmentosPremios();
                        $segmentoPremios->BNF3_Campania_id = $campaniaPremios->id;
                        $segmentoPremios->NombreSegmento = trim($value);
                        $segmentoPremios->CantidadPremios = $classicPtos[$key];
                        $segmentoPremios->CantidadPersonas = $classicPers[$key];
                        $segmentoPremios->Subtotal = $classicPtos[$key] * $classicPers[$key];
                        $segmentoPremios->Comentario = $classicComment[$key];
                        $segmentoPremios->Eliminado = '0';

                        $idSegmento = $this->getSegmetosTable()->saveSegmentoP($segmentoPremios);

                        $segmentoLog = new SegmentosPremiosLog();
                        $segmentoLog->BNF3_Segmentos_id = $idSegmento;
                        $segmentoLog->BNF3_Campania_id = $campaniaPremios->id;
                        $segmentoLog->NombreSegmento = trim($value);
                        $segmentoLog->CantidadPremios = $classicPtos[$key];
                        $segmentoLog->CantidadPersonas = $classicPers[$key];
                        $segmentoLog->Subtotal = $classicPtos[$key] * $classicPers[$key];
                        $segmentoLog->Comentario = $classicComment[$key];
                        $segmentoLog->Eliminado = 0;
                        $segmentoLog->RazonEliminado = "Creación del Segmento";
                        $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);

                        $segmentos = $contador > 0 ?
                            $segmentos . '; ' . trim($value) : trim($value);
                        $contador++;
                    }
                } elseif ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_TWO) {
                    $segmentoPremios = new SegmentosPremios();
                    $segmentoPremios->BNF3_Campania_id = $campaniaPremios->id;
                    $segmentoPremios->NombreSegmento = 'Personalizada';
                    $segmentoPremios->CantidadPremios = 0;
                    $segmentoPremios->CantidadPersonas = 0;
                    $segmentoPremios->Subtotal = (int)$request->getPost()->PresupuestoNegociado;
                    $segmentoPremios->Eliminado = '0';

                    $idSegmento = $this->getSegmetosTable()->saveSegmentoP($segmentoPremios);

                    $segmentoLog = new SegmentosPremiosLog();
                    $segmentoLog->BNF3_Segmentos_id = $idSegmento;
                    $segmentoLog->BNF3_Campania_id = $campaniaPremios->id;
                    $segmentoLog->NombreSegmento = 'Personalizada';
                    $segmentoLog->CantidadPremios = 0;
                    $segmentoLog->CantidadPersonas = 0;
                    $segmentoLog->Subtotal = (int)$request->getPost()->PresupuestoNegociado;
                    $segmentoLog->Eliminado = 0;
                    $segmentoLog->Comentario = "";
                    $segmentoLog->RazonEliminado = "Creación del Segmento";
                    $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);

                    $segmentos = 'Personalizada';
                }

                $campaniaLog = new CampaniaPremiosLog();
                $campaniaLog->BNF3_Campania_id = $id;
                $campaniaLog->NombreCampania = $campaniaPremios->NombreCampania;
                $campaniaLog->TipoSegmento = $campaniaPremios->TipoSegmento;
                $campaniaLog->FechaCampania = $campaniaPremios->FechaCampania;
                $campaniaLog->VigenciaInicio = $campaniaPremios->VigenciaInicio;
                $campaniaLog->VigenciaFin = $campaniaPremios->VigenciaFin;
                $campaniaLog->PresupuestoNegociado = (int)$campaniaPremios->PresupuestoNegociado;
                $campaniaLog->PresupuestoAsignado = (int)$campaniaPremios->PresupuestoAsignado;
                $campaniaLog->ParametroAlerta = (int)$campaniaPremios->ParametroAlerta;
                $campaniaLog->Comentario = $campaniaPremios->Comentario;
                $campaniaLog->Relacionado = (int)$campaniaPremios->Relacionado;
                $campaniaLog->EstadoCampania = $campaniaPremios->EstadoCampania;
                $campaniaLog->BNF_Empresa_id = $request->getPost()->Empresa;
                $campaniaLog->Segmentos = $segmentos;
                $campaniaLog->RazonEliminado = "Creación de Campaña";
                $this->getCampaniaLogTable()->saveCampaniaPremiosLog($campaniaLog);

                //Confirmacion del Registro
                $confirm[] = $this::MESSAGE_SAVE;
                $type = "success";
                $form = new FormCampaniasPR('registrar', $datos[0]);
                $typeSeg = "";
                $totalSeg = 0;
                $classicSeg = [];
                $classicPtos = [];
                $classicPers = [];
                $classicComment = [];
                $classicSegMessage = [];
                $classicPtosMessage = [];
                $classicPersMessage = [];
            } else {
                $confirm[] = $this::MESSAGE_ERROR;
                $type = "danger";
                $typeSeg = $request->getPost()->TipoSegmento;
                if ($typeSeg == $this::SEGMENT_TYPE_ONE) {
                    $totalSeg = count($request->getPost()->classicSeg);
                    $classicSeg = $this->generarArreglosJS($request->getPost()->classicSeg);
                    $classicPtos = $this->generarArreglosJS($request->getPost()->classicPtos);
                    $classicPers = $this->generarArreglosJS($request->getPost()->classicPers);
                    $classicComment = $this->generarArreglosJS($request->getPost()->classicComment);
                    $classicSegMessage = $this->generarArreglosJS($message["classicSeg"]);
                    $classicPtosMessage = $this->generarArreglosJS($message["classicPtos"]);
                    $classicPersMessage = $this->generarArreglosJS($message["classicPers"]);
                }
            }
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'premios_camp' => 'active',
                'camptosadd' => 'active',
                'form' => $form,
                'confirm' => $confirm,
                'type' => $type,
                'dataClassicSeg' => $classicSeg,
                'dataClassicPtos' => $classicPtos,
                'dataClassicPers' => $classicPers,
                'dataClassicComment' => $classicComment,
                'dataClassicSegMessage' => $classicSegMessage,
                'dataClassicPtosMessage' => $classicPtosMessage,
                'dataClassicPersMessage' => $classicPersMessage,
                'dataClassicCommentMessage' => $classicCommentMessage,
                'errorPersonalized' => $errorPersonalized,
                'personalizedSub' => $personalizedSub,
                'typeSeg' => $typeSeg,
                'totalSeg' => $totalSeg,
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $confirm = null;
        $type = null;
        $message = array();
        $totalSeg = 0;

        $errorPersonalized = null;
        $personalizedId = null;
        $personalizedSub = null;

        $classicSeg = array();
        $classicPtos = array();
        $classicPers = array();
        $classicComment = array();

        $tipoSegmentoMessage = null;
        $classicAsigMessage = array();
        $ofertaSegmentoMessage = array();

        $classicSegMessage = "[]";
        $classicPtosMessage = "[]";
        $classicPersMessage = "[]";

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('campanias-premios', array('action' => 'add'));
        }

        try {
            $campania = $this->getCampaniaTable()->getCampaniasP($id);
            $campania->VigenciaInicio = date_format(date_create($campania->VigenciaInicio), 'Y-m-d');
            $campania->VigenciaFin = date_format(date_create($campania->VigenciaFin), 'Y-m-d');
            $presupuestoAsignado = $this->getCampaniaTable()->getPresupuestoAcumulado($id);
            $campaniasEmp = $this->getCampaniaEmpresaTable()->getbyCampaniasP($id);
            $campaniasLog = $this->getCampaniaLogTable()->getCampaniaPremiosLogByCampania($id);
            $razon = (($campania->EstadoCampania == "Eliminado" or $campania->EstadoCampania == "Caducado")
                and is_object($campaniasLog)) ? $campaniasLog->RazonEliminado : "";

            $segmentosCampania = $this->getSegmetosTable()->getDetalleSegmentoInCampania($id);

            $totalAsignacion = $this->getAsignacionTable()->getAsignacionByCampania($id);
            $tieneAsignacion = $totalAsignacion > 0 ? true : false;

            $empresaAnterior = $campaniasEmp->BNF_Empresa_id;
            $typeSeg = $campania->TipoSegmento;
            $opcion = ((int)$campania->Eliminado == 1) ? false : true;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('campanias-premios', array('action' => 'index'));
        }

        $datos = $this->inicializacion();
        $form = new FormCampaniasPR('editar', $datos[0]);
        $form->bind($campania);
        $form->get('Empresa')->setAttribute('value', $campaniasEmp->BNF_Empresa_id);
        $form->get('PresupuestoAsignado')->setAttribute('value', (int)$presupuestoAsignado->PresupuestoAsignado);
        $form->get('submit')->setAttribute('value', 'Editar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $validate = new CampaniasPremiosFilter();
            $form->setInputFilter($validate->getInputFilter($datos[1]));
            $form->setData($request->getPost());

            $approved = false;
            if ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_ONE) {
                //Validación de Campos
                $resultados = $this->validarCampos($request);
                $approved = $resultados[0];
                $message = $resultados[1];
            } elseif ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_TWO) {
                $approved = true;
            }

            $tipoAnterior = $campania->TipoSegmento;

            //Validar Tipo con Asignaciones
            if ($tieneAsignacion) {
                if ($tipoAnterior == $request->getPost()->TipoSegmento) {
                    $message["tipoSegmento"] = "";
                    $tipoValid = true;
                } else {
                    $tipoValid = false;
                    $message["tipoSegmento"] = "No puede cambiar el tipo de Segmento. "
                        . "Los Segmentos tienen asignaciones";
                }

                if ($empresaAnterior == $request->getPost()->Empresa) {
                    $empresaValid = true;
                } else {
                    $empresaValid = false;
                }
            } else {
                $tipoValid = true;
                $empresaValid = true;
            }

            if ($form->isValid() && $approved && $tipoValid && $empresaValid) {
                $actualizacion = false;
                $mensaje = "";
                $tipoMensaje = "error";

                $campaniaPremios = $this->getCampaniaTable()->getCampaniasP($id);

                if ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_ONE) {
                    if ($tipoAnterior == $this::SEGMENT_TYPE_TWO) {
                        $dataSegmento = $this->getSegmetosTable()->getAllSegmentos($campaniaPremios->id);
                        foreach ($dataSegmento as $segmento) {
                            $segmentoLog = new SegmentosPremiosLog();
                            $segmentoLog->BNF3_Segmentos_id = $segmento->id;
                            $segmentoLog->BNF3_Campania_id = $segmento->BNF3_Campania_id;
                            $segmentoLog->NombreSegmento = $segmento->NombreSegmento;
                            $segmentoLog->CantidadPremios = (int)$segmento->CantidadPremios;
                            $segmentoLog->CantidadPersonas = (int)$segmento->CantidadPersonas;
                            $segmentoLog->Subtotal = (int)$segmento->Subtotal;
                            $segmentoLog->Eliminado = 1;
                            $segmentoLog->Comentario = isset($segmento->Comentario) ? $segmento->Comentario : "";
                            $segmentoLog->RazonEliminado = "Cambio de Tipo Campaña";
                            $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);
                        }

                        $this->getSegmetosTable()->disabledSegmentoP($campaniaPremios->id);
                    }

                    if (count($request->getPost()->classicSeg) > 0) {
                        $classicSeg = $request->getPost()->classicSeg;
                        $classicPtos = $request->getPost()->classicPtos;
                        $classicPers = $request->getPost()->classicPers;
                        $classicComment = $request->getPost()->classicComment;
                        foreach ($classicSeg as $key => $value) {
                            $segmentoPremios = new SegmentosPremios();
                            $segmentoPremios->BNF3_Campania_id = $campaniaPremios->id;
                            $segmentoPremios->NombreSegmento = trim($value);
                            $segmentoPremios->CantidadPremios = $classicPtos[$key];
                            $segmentoPremios->CantidadPersonas = $classicPers[$key];
                            $segmentoPremios->Subtotal = $classicPtos[$key] * $classicPers[$key];
                            $segmentoPremios->Comentario = $classicComment[$key];
                            $segmentoPremios->Eliminado = '0';

                            $idSegmento = $this->getSegmetosTable()->saveSegmentoP($segmentoPremios);

                            $segmentoLog = new SegmentosPremiosLog();
                            $segmentoLog->BNF3_Segmentos_id = $idSegmento;
                            $segmentoLog->BNF3_Campania_id = $campaniaPremios->id;
                            $segmentoLog->NombreSegmento = trim($value);
                            $segmentoLog->CantidadPremios = $classicPtos[$key];
                            $segmentoLog->CantidadPersonas = $classicPers[$key];
                            $segmentoLog->Subtotal = $classicPtos[$key] * $classicPers[$key];
                            $segmentoLog->Comentario = $classicComment[$key];
                            $segmentoLog->Eliminado = 0;
                            $segmentoLog->RazonEliminado = "Creación del Segmento";
                            $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);
                        }
                    }

                    $actualizacion = true;
                    $tipoMensaje = "success";
                    $mensaje = $this::MESSAGE_UPDATE;
                } elseif ($request->getPost()->TipoSegmento == $this::SEGMENT_TYPE_TWO) {
                    if ($tipoAnterior == $this::SEGMENT_TYPE_ONE) {
                        $dataSegmento = $this->getSegmetosTable()->getAllSegmentos($campaniaPremios->id);
                        foreach ($dataSegmento as $segmento) {
                            $segmentoLog = new SegmentosPremiosLog();
                            $segmentoLog->BNF3_Segmentos_id = $segmento->id;
                            $segmentoLog->BNF3_Campania_id = $segmento->BNF3_Campania_id;
                            $segmentoLog->NombreSegmento = $segmento->NombreSegmento;
                            $segmentoLog->CantidadPremios = (int)$segmento->CantidadPremios;
                            $segmentoLog->CantidadPersonas = (int)$segmento->CantidadPersonas;
                            $segmentoLog->Subtotal = (int)$segmento->Subtotal;
                            $segmentoLog->Eliminado = 1;
                            $segmentoLog->Comentario = isset($segmento->Comentario) ? $segmento->Comentario : "";
                            $segmentoLog->RazonEliminado = "Cambio de Tipo Campaña";
                            $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);
                        }

                        $this->getSegmetosTable()->disabledSegmentoP($campaniaPremios->id);

                        $segmentoPremios = new SegmentosPremios();
                        $segmentoPremios->BNF3_Campania_id = $campaniaPremios->id;
                        $segmentoPremios->NombreSegmento = 'Personalizada';
                        $segmentoPremios->CantidadPremios = 0;
                        $segmentoPremios->CantidadPersonas = 0;
                        $segmentoPremios->Subtotal = (int)$request->getPost()->PresupuestoNegociado;
                        $segmentoPremios->Eliminado = '0';

                        $idSegmento = $this->getSegmetosTable()->saveSegmentoP($segmentoPremios);

                        $segmentoLog = new SegmentosPremiosLog();
                        $segmentoLog->BNF3_Segmentos_id = $idSegmento;
                        $segmentoLog->BNF3_Campania_id = $campaniaPremios->id;
                        $segmentoLog->NombreSegmento = 'Personalizada';
                        $segmentoLog->CantidadPremios = 0;
                        $segmentoLog->CantidadPersonas = 0;
                        $segmentoLog->Subtotal = (int)$request->getPost()->PresupuestoNegociado;
                        $segmentoLog->Eliminado = 0;
                        $segmentoLog->Comentario = "";
                        $segmentoLog->RazonEliminado = "Creación del Segmento";
                        $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);
                    } else {
                        $dataSegmento = $this->getSegmetosTable()->getAllSegmentos($campaniaPremios->id);
                        foreach ($dataSegmento as $segmento) {
                            $segmento->BNF3_Campania_id = $campaniaPremios->id;
                            $segmento->NombreSegmento = 'Personalizada';
                            $segmento->CantidadPremios = 0;
                            $segmento->CantidadPersonas = 0;
                            $segmento->Subtotal = (int)$request->getPost()->PresupuestoNegociado;
                            $segmento->Eliminado = '0';

                            $idSegmento = $this->getSegmetosTable()->saveSegmentoP($segmento);

                            $segmentoLog = new SegmentosPremiosLog();
                            $segmentoLog->BNF3_Segmentos_id = $idSegmento;
                            $segmentoLog->BNF3_Campania_id = $campaniaPremios->id;
                            $segmentoLog->NombreSegmento = 'Personalizada';
                            $segmentoLog->CantidadPremios = 0;
                            $segmentoLog->CantidadPersonas = 0;
                            $segmentoLog->Subtotal = (int)$request->getPost()->PresupuestoNegociado;
                            $segmentoLog->Eliminado = 0;
                            $segmentoLog->Comentario = "";
                            $segmentoLog->RazonEliminado = "Actualización del Segmento";
                            $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);
                        }
                    }

                    $actualizacion = true;
                    $tipoMensaje = "success";
                    $mensaje = $this::MESSAGE_UPDATE;
                }

                if ($actualizacion) {
                    //Agregar CampaniasPremios
                    $campaniaPremios->NombreCampania = trim($request->getPost()->NombreCampania);
                    $campaniaPremios->TipoSegmento = $request->getPost()->TipoSegmento;
                    $campaniaPremios->FechaCampania = $request->getPost()->FechaCampania;
                    $campaniaPremios->VigenciaInicio = $request->getPost()->VigenciaInicio;
                    $campaniaPremios->VigenciaFin = $request->getPost()->VigenciaFin;
                    $campaniaPremios->PresupuestoNegociado = (int)$request->getPost()->PresupuestoNegociado;
                    $campaniaPremios->ParametroAlerta = (int)$request->getPost()->ParametroAlerta;
                    $campaniaPremios->Comentario = $request->getPost()->Comentario;
                    $campaniaPremios->Relacionado = (int)$request->getPost()->Relacionado;
                    $campaniaPremios->EstadoCampania = $request->getPost()->EstadoCampania;
                    $this->getCampaniaTable()->saveCampaniasP($campaniaPremios);

                    //Agregando Relacion CampaniaP Empresa
                    $campaniasEmpresa = $this->getCampaniaEmpresaTable()->getCampaniasPremiosEmpresas($campaniasEmp->id);
                    $campaniasEmpresa->BNF3_Campania_id = $id;
                    $campaniasEmpresa->BNF_Empresa_id = $request->getPost()->Empresa;
                    $this->getCampaniaEmpresaTable()->saveCampaniasPremiosEmpresas($campaniasEmpresa);

                    $dataSegmento = $this->getSegmetosTable()->getAllSegmentosCampania($id);
                    $segmentos = "";
                    $contador = 0;
                    foreach ($dataSegmento as $seg) {
                        $segmentos = $contador > 0 ?
                            $segmentos . '; ' . $seg->NombreSegmento : $seg->NombreSegmento;
                        $contador++;
                    }

                    $campaniaLog = new CampaniaPremiosLog();
                    $campaniaLog->BNF3_Campania_id = $id;
                    $campaniaLog->NombreCampania = $campaniaPremios->NombreCampania;
                    $campaniaLog->TipoSegmento = $campaniaPremios->TipoSegmento;
                    $campaniaLog->FechaCampania = $campaniaPremios->FechaCampania;
                    $campaniaLog->VigenciaInicio = $campaniaPremios->VigenciaInicio;
                    $campaniaLog->VigenciaFin = $campaniaPremios->VigenciaFin;
                    $campaniaLog->PresupuestoNegociado = (int)$campaniaPremios->PresupuestoNegociado;
                    $campaniaLog->PresupuestoAsignado = (int)$campaniaPremios->PresupuestoAsignado;
                    $campaniaLog->ParametroAlerta = (int)$campaniaPremios->ParametroAlerta;
                    $campaniaLog->Comentario = $campaniaPremios->Comentario;
                    $campaniaLog->Relacionado = (int)$campaniaPremios->Relacionado;
                    $campaniaLog->EstadoCampania = $campaniaPremios->EstadoCampania;
                    $campaniaLog->BNF_Empresa_id = $request->getPost()->Empresa;
                    $campaniaLog->Segmentos = $segmentos;
                    $campaniaLog->RazonEliminado = "Edición de Campaña";
                    $this->getCampaniaLogTable()->saveCampaniaPremiosLog($campaniaLog);
                }

                //Confirmación del Registro
                $confirm[] = $mensaje;
                $type = $tipoMensaje;
                $campaniasEmp = $this->getCampaniaEmpresaTable()->getbyCampaniasP($id);
                $segmentosCampania = $this->getSegmetosTable()->getDetalleSegmentoInCampania($id);

                $totalSeg = 0;
                $typeSeg = $campania->TipoSegmento;
                $classicSeg = array();
                $classicPtos = array();
                $classicPers = array();
                $classicComment = array();

                $form->get('Empresa')->setAttribute('value', $campaniasEmp->BNF_Empresa_id);
            } else {
                $confirm[] = $this::MESSAGE_ERROR;
                $type = "danger";
                $typeSeg = $request->getPost()->TipoSegmento;
                if ($typeSeg == $this::SEGMENT_TYPE_ONE) {
                    if (!$tipoValid) {
                        $tipoSegmentoMessage = $message["tipoSegmento"];
                        $typeSeg = $tipoAnterior;
                        $form->get('TipoSegmento')->setAttribute('value', $tipoAnterior);
                    } else {
                        $totalSeg = count($request->getPost()->classicSeg);
                        if ($totalSeg > 0) {
                            $classicSeg = $this->generarArreglosJS($request->getPost()->classicSeg);
                            $classicPtos = $this->generarArreglosJS($request->getPost()->classicPtos);
                            $classicPers = $this->generarArreglosJS($request->getPost()->classicPers);
                            $classicSegMessage = $this->generarArreglosJS($message["classicSeg"]);
                            $classicPtosMessage = $this->generarArreglosJS($message["classicPtos"]);
                            $classicPersMessage = $this->generarArreglosJS($message["classicPers"]);
                        }
                    }
                } elseif ($typeSeg == $this::SEGMENT_TYPE_TWO) {
                    if (!$tipoValid) {
                        $tipoSegmentoMessage = $message["tipoSegmento"];
                        $typeSeg = $tipoAnterior;
                        $form->get('TipoSegmento')->setAttribute('value', $tipoAnterior);
                    }
                }

                if (!$empresaValid) {
                    $form->get('Empresa')->setAttribute('value', $empresaAnterior);
                    $form->get('Empresa')->setMessages(
                        array("No puede cambiar la empresa. La Campaña ya tiene asignaciones")
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'premios_camp' => 'active',
                'camptosadd' => 'active',
                'form' => $form,
                'confirm' => $confirm,
                'type' => $type,
                'elemento' => "Campaña",
                'opcion' => $opcion,
                'id' => $id,
                'dataClassicSeg' => $classicSeg,
                'dataClassicPtos' => $classicPtos,
                'dataClassicPers' => $classicPers,
                'dataClassicComment' => $classicComment,
                'dataClassicSegMessage' => $classicSegMessage,
                'dataClassicPtosMessage' => $classicPtosMessage,
                'dataClassicPersMessage' => $classicPersMessage,
                'dataAsignacionClasica' => $classicAsigMessage,
                'dataTipoSegmentoMessage' => $tipoSegmentoMessage,
                'dataOfertaSegmentoMessage' => $ofertaSegmentoMessage,
                'segmentosCampania' => $segmentosCampania,
                'razon' => $razon,
                'typeSeg' => $typeSeg,
                'totalSeg' => $totalSeg,
            )
        );
    }

    public function editSegmentoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $confirm = null;
        $type = null;

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('campanias-premios', array('action' => 'add'));
        }

        try {
            $segmento = $this->getSegmetosTable()->getDetalleSegmentoAsignacion($id);
            if (is_object($segmento)) {
                $nombreSegmento = $segmento->NombreSegmento;
                $premiosUsuarios = $segmento->CantidadPremios;
                $cantidadUsuarios = $segmento->CantidadPersonas;
                $disponible = $segmento->Subtotal - $segmento->AsignadoActivo
                    - $segmento->AsignadoEliminado - $segmento->AplicadoInactivo;
                $aplicado = $segmento->AplicadoActivo;

                $campania = $this->getCampaniaTable()->getCampaniasP($segmento->BNF3_Campania_id);
                $campaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($campania->id);
                $empresa = $this->getEmpresaTable()->getEmpresa($campaniaEmpresa->BNF_Empresa_id);
                $asignaciones = $this->getAsignacionTable()->getAsignacionBySegmento($id)->count();
            } else {
                return $this->redirect()->toRoute('campanias-premios', array('action' => 'index'));
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('campanias-premios', array('action' => 'index'));
        }

        $form = new FormSegmentoP();
        $form->bind($segmento);
        if ($segmento->Eliminado == 1) {
            $form->get('NombreSegmento')->setAttribute('disabled', true);
            $form->get('Comentario')->setAttribute('disabled', true);
            $form->get('CantidadPersonas')->setAttribute('disabled', true);
            $form->get('CantidadPremios')->setAttribute('disabled', true);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $validate = new SegmentosPremiosFilter();
            $form->setInputFilter($validate->getInputFilter());
            $form->setData($request->getPost());

            $resultado = $this->validarAsignacionCampos($campania->id, $request, $disponible, $aplicado);
            $asignacionValida = $resultado[0];
            $asignacionMensaje = $resultado[1];
            if ($form->isValid()) {
                $segmento = $this->getSegmetosTable()->getDetalleSegmentoAsignacion($id);
                $segmento->NombreSegmento = trim($request->getPost()->NombreSegmento);
                $segmento->Comentario = $request->getPost()->Comentario;

                if ($asignaciones > 0) {
                    if ($asignacionValida) {
                        $diferencia = $request->getPost()->CantidadPremios - $segmento->CantidadPremios;
                        $diferenciaUsuarios = $request->getPost()->CantidadPersonas - $segmento->CantidadPersonas;

                        if ($diferencia > 0 or $diferenciaUsuarios > 0) {
                            $segmento->CantidadPremios = $request->getPost()->CantidadPremios;
                            $segmento->CantidadPersonas = $request->getPost()->CantidadPersonas;
                            $segmento->Subtotal = $request->getPost()->CantidadPremios * $request->getPost()->CantidadPersonas;

                            if ($diferencia > 0) {
                                $asignaciones = $this->getAsignacionTable()->getAsignacionBySegmento($id);
                                foreach ($asignaciones as $value) {
                                    if ($value->Eliminado == 0) {
                                        $value->CantidadPremios = $request->getPost()->CantidadPremios;
                                        $value->CantidadPremiosDisponibles = $diferencia + $value->CantidadPremiosDisponibles;
                                        $value->Eliminado = (int)$value->Eliminado;
                                        $this->getAsignacionTable()->saveAsignacion($value);

                                        $asignacion = $value;
                                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $asignacion->id;
                                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                        $AsignacionPremiosEstadoLog->CantidadPremios = (int)$asignacion->CantidadPremios;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = (int)$asignacion->CantidadPremiosUsados;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = (int)$asignacion->CantidadPremiosDisponibles;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = (int)$asignacion->CantidadPremiosEliminados;
                                        $AsignacionPremiosEstadoLog->EstadoPremios = $asignacion->EstadoPremios;
                                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                                        $AsignacionPremiosEstadoLog->Premios = (int)$diferencia;
                                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $AsignacionPremiosEstadoLog->Motivo = "Edición del Segmento";
                                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                                    }
                                }
                            }
                            $mensaje = "El segmento fue editado correctamente.";
                            $type = "success";
                        } elseif ($diferencia < 0) {
                            $mensaje = "No se puede reducir la cantidad de premios del segmento, porque cuenta con asignaciones.";
                            $type = "danger";
                        } elseif ($diferenciaUsuarios < 0) {
                            $mensaje = "No se puede reducir la cantidad de usuarios del segmento, porque cuenta con asignaciones.";
                            $type = "danger";
                        } else {
                            $mensaje = "El segmento fue editado correctamente.";
                            $type = "success";
                        }
                    } else {
                        $mensaje = array_pop($asignacionMensaje);
                        $type = "danger";
                    }
                } else {
                    $segmento->CantidadPremios = $request->getPost()->CantidadPremios;
                    $segmento->CantidadPersonas = $request->getPost()->CantidadPersonas;
                    $segmento->Subtotal = $request->getPost()->CantidadPremios * $request->getPost()->CantidadPersonas;
                    $mensaje = "El segmento fue editado correctamente.";
                    $type = "success";
                }

                $this->getSegmetosTable()->saveSegmentoP($segmento);

                $segmentoLog = new SegmentosPremiosLog();
                $segmentoLog->BNF3_Segmentos_id = $segmento->id;
                $segmentoLog->BNF3_Campania_id = $segmento->BNF3_Campania_id;
                $segmentoLog->NombreSegmento = $segmento->NombreSegmento;
                $segmentoLog->CantidadPremios = $segmento->CantidadPremios;
                $segmentoLog->CantidadPersonas = $segmento->CantidadPersonas;
                $segmentoLog->Subtotal = $segmento->Subtotal;
                $segmentoLog->Comentario = $segmento->Comentario;
                $segmentoLog->Eliminado = (int)$segmento->Eliminado;
                $segmentoLog->RazonEliminado = "Edición del Segmento";
                $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);

                $confirm[] = $mensaje;
            } else {
                $confirm = $asignacionMensaje;
                $type = "danger";
                $segmento->NombreSegmento = $nombreSegmento;
                $segmento->CantidadPremios = $premiosUsuarios;
                $segmento->CantidadPersonas = $cantidadUsuarios;
                $form->bind($segmento);
            }

            $segmento = $this->getSegmetosTable()->getDetalleSegmentoAsignacion($id);
            $form->bind($segmento);
            $campania = $this->getCampaniaTable()->getCampaniasP($segmento->BNF3_Campania_id);
            $campaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($campania->id);
            $empresa = $this->getEmpresaTable()->getEmpresa($campaniaEmpresa->BNF_Empresa_id);
        }

        $dataEmpresa = $empresa->NombreComercial . " - " . $empresa->RazonSocial . " - " . $empresa->Ruc;
        $dataCampania = $campania->NombreCampania;
        $idCampania = $campania->id;
        return new ViewModel(
            array(
                'premios' => 'active',
                'premios_camp' => 'active',
                'camptosadd' => 'active',
                'form' => $form,
                'confirm' => $confirm,
                'type' => $type,
                'elemento' => "Segmento",
                'id' => $id,
                'empresa' => $dataEmpresa,
                'campania' => $dataCampania,
                'segmento' => $segmento,
                'idCampania' => $idCampania,
            )
        );
    }

    public function deleteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            $comment = isset($post_data['comment']) ? $post_data['comment'] : "";
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])) {
                    $result = $this->getCampaniaTable()->getCampaniasP($id);
                    if (is_object($result)) {
                        $presupuestoAsignado = $this->getCampaniaTable()->getPresupuestoAcumulado($id);
                        $asignados = $presupuestoAsignado->PresupuestoAsignado;

                        $dataSegmento = $this->getCampaniaEmpresaTable()->getbyCampaniasP($id);
                        $empresa = $dataSegmento->BNF_Empresa_id;

                        $dataSegmento = $this->getSegmetosTable()->getAllSegmentos($id);
                        $segmentos = "";
                        $contador = 0;
                        foreach ($dataSegmento as $value) {
                            $segmentos = $contador > 0 ?
                                $segmentos . '; ' . $value->NombreSegmento : $value->NombreSegmento;

                            $ofertas = $this->getOfertaPremiosTable()->getAllOfertaPremiosBySegmento($value->id);
                            if (count($ofertas) > 0) {
                                foreach ($ofertas as $oferta) {

                                    $ofertaSegmento = $this->getOfertaPremiosSegmentoTable()
                                        ->getOfertaPremiosSegmentoSearch($oferta->id, $value->id);

                                    $this->getOfertaPremiosSegmentoTable()->deleteOfertaPremiosSegmento($ofertaSegmento->id);
                                }
                            }

                            $asignaciones = $this->getAsignacionTable()->getAsignacionBySegmento($value->id);
                            $estadoPremios = "Cancelado";
                            foreach ($asignaciones as $asignacion) {
                                if ($asignacion->EstadoPremios != $estadoPremios) {
                                    $premiosDisponibles = $asignacion->CantidadPremiosDisponibles;

                                    $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                    $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $asignacion->id;
                                    $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                                    $AsignacionPremiosEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                    $AsignacionPremiosEstadoLog->CantidadPremios = 0;
                                    $AsignacionPremiosEstadoLog->CantidadPremiosUsados = (int)$asignacion->CantidadPremiosUsados;
                                    $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = 0;
                                    $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = (int)$asignacion->CantidadPremiosDisponibles;
                                    $AsignacionPremiosEstadoLog->EstadoPremios = $estadoPremios;
                                    $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_CANCELAR;
                                    $AsignacionPremiosEstadoLog->Premios = (int)$asignacion->CantidadPremiosDisponibles;
                                    $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                    $AsignacionPremiosEstadoLog->Motivo = "Eliminación de la Campaña";
                                    $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                                    $this->getAsignacionTable()
                                        ->cambiarEstadoPremiosAsignacion($asignacion->id, $estadoPremios, $premiosDisponibles);
                                }
                            }

                            $segmentoLog = new SegmentosPremiosLog();
                            $segmentoLog->BNF3_Segmentos_id = $value->id;
                            $segmentoLog->BNF3_Campania_id = $id;
                            $segmentoLog->NombreSegmento = $value->NombreSegmento;
                            $segmentoLog->CantidadPremios = (int)$value->CantidadPremios;
                            $segmentoLog->CantidadPersonas = (int)$value->CantidadPersonas;
                            $segmentoLog->Subtotal = $value->Subtotal;
                            $segmentoLog->Comentario = isset($value->Comentario) ? $value->Comentario : "";
                            $segmentoLog->Eliminado = 1;
                            $segmentoLog->RazonEliminado = "Eliminación por campaña";
                            $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);

                            $contador++;
                        }

                        $this->getCampaniaTable()->deleteCampaniasP($id);
                        $this->getSegmetosTable()->disabledSegmentoP($id);

                        $campaniaLog = new CampaniaPremiosLog();
                        $campaniaLog->BNF3_Campania_id = $id;
                        $campaniaLog->NombreCampania = $result->NombreCampania;
                        $campaniaLog->TipoSegmento = $result->TipoSegmento;
                        $campaniaLog->FechaCampania = $result->FechaCampania;
                        $campaniaLog->VigenciaInicio = $result->VigenciaInicio;
                        $campaniaLog->VigenciaFin = $result->VigenciaFin;
                        $campaniaLog->PresupuestoNegociado = (int)$result->PresupuestoNegociado;
                        $campaniaLog->PresupuestoAsignado = (int)$asignados;
                        $campaniaLog->ParametroAlerta = (int)$result->ParametroAlerta;
                        $campaniaLog->Comentario = isset($result->Comentario) ? $result->Comentario : "";
                        $campaniaLog->Relacionado = (int)$result->Relacionado;
                        $campaniaLog->EstadoCampania = 'Eliminado';
                        $campaniaLog->BNF_Empresa_id = $empresa;
                        $campaniaLog->Segmentos = $segmentos;
                        $campaniaLog->RazonEliminado = $comment;
                        $this->getCampaniaLogTable()->saveCampaniaPremiosLog($campaniaLog);
                        $state = true;
                        $this->flashMessenger()->addMessage("Campaña eliminada correctamente");
                    } else {
                        $state = false;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function deleteSegmentoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            $comment = isset($post_data['comment']) ? $post_data['comment'] : "";
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])) {
                    $result = $this->getSegmetosTable()->getSegmentosPremios($id);
                    if (is_object($result)) {
                        $this->getSegmetosTable()->deleteSegmentoP($id);

                        $asignaciones = $this->getAsignacionTable()->getAsignacionBySegmento($id);
                        $estadoPremios = "Cancelado";
                        foreach ($asignaciones as $asignacion) {
                            if ($asignacion->EstadoPremios != $estadoPremios) {
                                $premiosDisponibles = $asignacion->CantidadPremiosDisponibles;

                                $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $asignacion->id;
                                $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                                $AsignacionPremiosEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                $AsignacionPremiosEstadoLog->CantidadPremios = 0;
                                $AsignacionPremiosEstadoLog->CantidadPremiosUsados = (int)$asignacion->CantidadPremiosUsados;
                                $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = 0;
                                $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = (int)$asignacion->CantidadPremiosDisponibles;
                                $AsignacionPremiosEstadoLog->EstadoPremios = $estadoPremios;
                                $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_CANCELAR;
                                $AsignacionPremiosEstadoLog->Premios = (int)$asignacion->CantidadPremiosDisponibles;
                                $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                $AsignacionPremiosEstadoLog->Motivo = "Eliminación del Segmento";
                                $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                                $this->getAsignacionTable()
                                    ->cambiarEstadoPremiosAsignacion($asignacion->id, $estadoPremios, $premiosDisponibles);
                            }
                        }

                        $segmentoLog = new SegmentosPremiosLog();
                        $segmentoLog->BNF3_Segmentos_id = $id;
                        $segmentoLog->BNF3_Campania_id = $result->BNF3_Campania_id;
                        $segmentoLog->NombreSegmento = $result->NombreSegmento;
                        $segmentoLog->CantidadPremios = (int)$result->CantidadPremios;
                        $segmentoLog->CantidadPersonas = (int)$result->CantidadPersonas;
                        $segmentoLog->Subtotal = (int)$result->Subtotal;
                        $segmentoLog->Eliminado = 1;
                        $segmentoLog->Comentario = isset($result->Comentario) ? $result->Comentario : "";
                        $segmentoLog->RazonEliminado = $comment;
                        $this->getSegmentoLogTable()->saveSegmentosPremiosLog($segmentoLog);

                        $this->flashMessenger()->addMessage("Segmento Eliminado Correctamente");
                        $state = true;
                    } else {
                        $state = false;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresa = (int)$this->params()->fromRoute('id', 0);
        $fecha = $this->params()->fromRoute('val', null);

        $resultado = $this->getCampaniaTable()->getReporte($empresa, $fecha);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Información del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Campañas Premios")
                ->setSubject("Campañas Premios")
                ->setDescription("Documento listando Campañas Premios")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Campañas Premios");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:H' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:H' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Empresa Cliente')
                ->setCellValue('C1', 'Nombre Campaña')
                ->setCellValue('D1', 'Presupuesto (Cantidad Premios)')
                ->setCellValue('E1', 'Vigencia Inicio')
                ->setCellValue('F1', 'Vigencia Fin')
                ->setCellValue('G1', 'Tipo Segmento')
                ->setCellValue('H1', 'Activo');
            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Empresa)
                    ->setCellValue('C' . $i, $registro->NombreCampania)
                    ->setCellValue('D' . $i, $registro->Presupuesto)
                    ->setCellValue('E' . $i, $registro->VigenciaInicio)
                    ->setCellValue('F' . $i, $registro->VigenciaFin)
                    ->setCellValue('G' . $i, $registro->TipoSegmento)
                    ->setCellValue('H' . $i, ((int)$registro->Eliminado == 1) ? 'Inactivo' : 'Activo');
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="CampaniaPremios.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function validarCampos($request)
    {
        $approved = false;
        $messages = array();

        $validRegex = new Regex(array('pattern' => "/^([a-zA-Z-0-9 ÑñÁáÉéÍíÓóÚú&\´\.\/'\,\-])+$/"));
        $validDigits = new IsInt();
        $validNotEmpty = new NotEmpty(NotEmpty::ALL);

        $classicSeg = $request->getPost()->classicSeg;
        $classicPtos = $request->getPost()->classicPtos;
        $classicPers = $request->getPost()->classicPers;
        $classicComment = $request->getPost()->classicComment;
        if (count($classicSeg) == count($classicPtos) and count($classicPtos) == count($classicPers)
            and count($classicPers) == count($classicComment)
        ) {
            //Validar Segmentos
            $classicSegState = true;
            if (is_array($classicSeg)) {
                foreach ($classicSeg as $value) {
                    if (!$validNotEmpty($value)) {
                        $messages['classicSeg'][] = "El campo no puede quedar vacío.";
                        $classicSegState = false;
                    } elseif (!$validRegex($value)) {
                        $messages['classicSeg'][] = "El valor ingresado: " . $value . ", no es válido.";
                        $classicSegState = false;
                    } else {
                        $messages['classicSeg'][] = "";
                    }
                }
            }
            //Validar Cantidad de Premios
            $classicPtosState = true;
            if (is_array($classicPtos)) {
                foreach ($classicPtos as $value) {
                    if (!$validNotEmpty($value)) {
                        $messages['classicPtos'][] = "El campo no puede quedar vacío.";
                        $classicPtosState = false;
                    } elseif (!$validDigits($value)) {
                        $messages['classicPtos'][] = "El campo solo acepta números enteros.";
                        $classicPtosState = false;
                    } else {
                        $messages['classicPtos'][] = "";
                    }
                }
            }
            //Validar Cantidad de Personas
            $classicPersState = true;
            if (is_array($classicPers)) {
                foreach ($classicPers as $value) {
                    if (!$validNotEmpty($value)) {
                        $messages['classicPers'][] = "El campo no puede quedar vacío.";
                        $classicPersState = false;
                    } elseif (!$validDigits($value)) {
                        $messages['classicPers'][] = "El campo solo acepta números enteros.";
                        $classicPersState = false;
                    } else {
                        $messages['classicPers'][] = "";
                    }
                }
            }
            //Comprobando validaciones
            if ($classicPersState and $classicPtosState and $classicSegState) {
                $approved = true;
            }
        }
        return array($approved, $messages);
    }

    public function validarAsignacionCampos($campania, $request, $disponible, $aplicado)
    {
        $totalAprobado = 0;

        $nombreSegmento = $request->getPost()->NombreSegmento;
        $datoSegmento = $this->getSegmetosTable()->getByName($campania, $nombreSegmento);
        if (is_object($datoSegmento)) {
            if ($disponible > 0 && $aplicado) {
                if ($request->getPost()->CantidadPremios >= $datoSegmento->CantidadPremios &&
                    $request->getPost()->CantidadPersonas >= $datoSegmento->CantidadPersonas
                ) {
                    $totalAprobado++;
                    $messages[] = "";
                } else {
                    $messages[] = "El segmento tiene premios aplicados, y no puede modificar sus premios " .
                        "por una cantidad menor";
                }
            } else {
                $totalAprobado++;
                $messages[] = "";
            }
        } else {
            $totalAprobado++;
            $messages[] = "";
        }

        //Verificar Total
        if ($totalAprobado != 0) {
            $approved = true;
        } else {
            $approved = false;
        }

        return array($approved, $messages);
    }

    public function validarOfertaSegmento($request, $campania, $segmentos)
    {
        $resultado = array();
        $messages = array();
        $totalAprobado = 0;

        $recibidos = $request->getPost()->classicSeg;
        foreach ($segmentos as $value) {
            if (!in_array($value, $recibidos)) {
                array_push($resultado, $value);
            }
        }

        //Verificar Total
        if (!empty($resultado)) {
            foreach ($resultado as $item) {
                $datoSegmento = $this->getSegmetosTable()->getByName($campania, $item);
                if (is_object($datoSegmento)) {
                    $ofertaSegmento = $this->getOfertaPremiosTable()->getOfertaPremiosBySegmento($datoSegmento->id);
                    if (is_object($ofertaSegmento)) {
                        $totalAprobado++;
                        $messages["ofertas"][] = "No se puede eliminar el segmento: " . $item .
                            ", porque ya esta asignada una oferta";
                    } else {
                        $messages["ofertas"][] = "";
                    }
                }
            }

            if ($totalAprobado == 0) {
                $approved = true;
            } else {
                $approved = false;
            }
        } else {
            $approved = true;
        }

        return array($approved, $messages);
    }

    public function generarArreglosJS($arreglo)
    {
        $temp = "[";
        $contador = 0;
        if (is_array($arreglo)) {
            foreach ($arreglo as $value) {
                $temp = $contador > 0 ? $temp . ", '" . addslashes($value) . "'" : $temp . "'" . addslashes($value) . "'";
                $contador++;
            }
        }
        $temp = $temp . "]";
        $arreglo = $temp;
        return $arreglo;
    }
}
