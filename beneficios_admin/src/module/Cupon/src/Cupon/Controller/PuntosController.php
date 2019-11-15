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
use Cupon\Form\BusquedaCuponPuntos;
use Cupon\Form\FormCuponPuntos;
use Cupon\Form\FormEnvioCuponPuntos;
use Cupon\Model\CuponPuntosLog;
use Puntos\Model\AsignacionEstadoLog;
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

class PuntosController extends AbstractActionController
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
    public function getCuponPuntosTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPuntosTable');
    }

    public function getCuponPuntosLogTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPuntosLogTable');
    }

    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosTable');
    }

    public function getOfertaPuntosSegmentoTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosSegmentoTable');
    }

    public function getOfertaPuntosAtributosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosAtributosTable');
    }

    public function getSegmentoPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\SegmentosPTable');
    }

    public function getCampaniaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPTable');
    }

    public function getConfiguracionesTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\ConfiguracionesTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\AsignacionTable');
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
        return $this->serviceLocator->get('Puntos\Model\Table\AsignacionEstadoLogTable');
    }

    public function getCuponPuntosAsignacionTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CuponPuntosAsignacionTable');
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
            return $this->redirect()->toRoute('cupon-puntos', array('action' => 'edit'));
        }

        $data = $this->inicializacionBusqueda();
        $empresaUsuario = $this->identity()->BNF_Empresa_id;
        if (!empty($empresaUsuario)) {
            $nombreEmpresa = $this->getEmpresaTable()->getEmpresa($empresaUsuario)->NombreComercial;
        }
        $formSearch = new BusquedaCuponPuntos("busqueda", $data[0], $empresaUsuario);
        $formEnviar = new FormEnvioCuponPuntos();

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
            $asignaciones = $this->getCuponPuntosTable()
                ->getListaCuponesPuntos($order_by, $order, $empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo);
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
                'puntos' => 'active',
                'cuponPuntos' => 'active',
                'listCuponPuntos' => 'active',
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

    public function reportoneAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $opcion = null;
        $nombreEmpresa = null;
        //$estadoCupon="Redimido";
        $estadoCupon="Redimido";
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
            return $this->redirect()->toRoute('cupon-puntos', array('action' => 'edit'));
        }

        $data = $this->inicializacionBusqueda();
        $empresaUsuario = $this->identity()->BNF_Empresa_id;
        if (!empty($empresaUsuario)) {
            $nombreEmpresa = $this->getEmpresaTable()->getEmpresa($empresaUsuario)->NombreComercial;
        }
        $formSearch = new BusquedaCuponPuntos("busqueda", $data[0], $empresaUsuario);
        $formEnviar = new FormEnvioCuponPuntos();

        $request = $this->getRequest();
        if ($request->isPost()) {

//            var_dump($request->getPost());exit;
            $empresa = !empty($request->getPost()->Empresa) ? $request->getPost()->Empresa : null;
            $estado =[$estadoCupon];
            $desde = !empty($request->getPost()->FechaInicio) ? $request->getPost()->FechaInicio : null;
            $hasta = !empty($request->getPost()->FechaFin) ? $request->getPost()->FechaFin : null;
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $estado = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
            $desde = $this->params()->fromRoute('q5') ? $this->params()->fromRoute('q5') : null;
            $hasta = $this->params()->fromRoute('q6') ? $this->params()->fromRoute('q6') : null;
            $estado = str_replace("-", " ", $estado);
        }

        $formSearch->setData(
            array(
                "Empresa" => isset($empresaUsuario) ? $empresaUsuario : $empresa,
                "EstadoCupon" => $estado,
                "FechaInicio" => $desde,
                "FechaFin" => $hasta,
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
            $asignaciones = $this->getCuponPuntosTable()
                ->getListaCuponesPuntosPorEstado($order_by, $order, $empresa,  $estado, $desde, $hasta);
            $paginator = new Paginator(new paginatorIterator($asignaciones, $order_by));

            $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);
        }

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }


//        var_dump($asignaciones->toArray()[0]['ComentarioUno']);exit;
        return new ViewModel(
            array(
                'reportone' => 'active',
                'puntos' => 'active',
                'cuponPuntos' => 'active',
                'asignaciones' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'formS' => $formSearch,
                'formE' => $formEnviar,
                'p' => $page,
                'q1' => $empresa,
                'q4' => "Redimido",
                'q5' => $desde,
                'q6' => $hasta,
                'opcion' => $opcion,
                'nombreEmpresa' => $nombreEmpresa,
            )
        );
    }


    public function reporttwoAction()
     {
         $identity = $this->identity();
         if (!$identity) {
             return $this->redirect()->toUrl('/login');
         }

         $opcion = null;
         $nombreEmpresa = null;
         //$estadoCupon="Redimido";
         $estadoCupon=["Por Pagar","Pagado"];
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
             return $this->redirect()->toRoute('cupon-puntos', array('action' => 'edit'));
         }

         $data = $this->inicializacionBusqueda();
         $empresaUsuario = $this->identity()->BNF_Empresa_id;
         if (!empty($empresaUsuario)) {
             $nombreEmpresa = $this->getEmpresaTable()->getEmpresa($empresaUsuario)->NombreComercial;
         }
         $formSearch = new BusquedaCuponPuntos("busqueda", $data[0], $empresaUsuario);
         $formEnviar = new FormEnvioCuponPuntos();

         $request = $this->getRequest();
         if ($request->isPost()) {

//            var_dump($request->getPost());exit;
             $empresa = !empty($request->getPost()->Empresa) ? $request->getPost()->Empresa : null;
             $estado =$estadoCupon;
             $desde = !empty($request->getPost()->FechaInicio) ? $request->getPost()->FechaInicio : null;
             $hasta = !empty($request->getPost()->FechaFin) ? $request->getPost()->FechaFin : null;
         } else {
             $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
             $estado = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
             $desde = $this->params()->fromRoute('q5') ? $this->params()->fromRoute('q5') : null;
             $hasta = $this->params()->fromRoute('q6') ? $this->params()->fromRoute('q6') : null;
             $estado = str_replace("-", " ", $estado);
         }

         $formSearch->setData(
             array(
                 "Empresa" => isset($empresaUsuario) ? $empresaUsuario : $empresa,
                 "EstadoCupon" => $estado,
                 "FechaInicio" => $desde,
                 "FechaFin" => $hasta,
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
             $asignaciones = $this->getCuponPuntosTable()
                 ->getListaCuponesPuntosPorEstado($order_by, $order, $empresa,  $estado, $desde, $hasta);
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
                 'puntos' => 'active',
                 'cuponPuntos' => 'active',
                 'reporttwo' => 'active',
                 'asignaciones' => $paginator,
                 'order_by' => $order_by_o,
                 'order' => $order,
                 'formS' => $formSearch,
                 'formE' => $formEnviar,
                 'p' => $page,
                 'q1' => $empresa,
                 'q4' => "Pagado",
                 'q5' => $desde,
                 'q6' => $hasta,
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
        $formC = new FormCuponPuntos($formName, $empresa);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $codigoCupon = $request->getPost()->cupon ? $request->getPost()->cupon : null;
            if ($codigoCupon != null) {
                $cupon = $this->getCuponPuntosTable()
                    ->searchCuponPuntos($codigoCupon, $this->identity()->BNF_Empresa_id);
                if (is_object($cupon)) {
                    $oferta = $this->getOfertaPuntosTable()->getOfertaPuntos($cupon->BNF2_Oferta_Puntos_id);

                    $empresaProv = $this->getEmpresaTable()->getEmpresa($oferta->BNF_Empresa_id)->NombreComercial;
                    $cuponLog = $this->getCuponPuntosLogTable()->getCuponPuntosLogByEstado($cupon->id, $cupon->EstadoCupon);

                    if ($oferta->TipoPrecio == "Unico") {
                        $precioCupon = $oferta->PrecioVentaPublico;
                        $precioBeneficio = $oferta->PrecioBeneficio;
                    } else {
                        $ofertaAtributo = $this->getOfertaPuntosAtributosTable()
                            ->getOfertaPuntosAtributos($cupon->BNF2_Oferta_Puntos_Atributos_id);
                        $precioCupon = $ofertaAtributo->PrecioVentaPublico;
                        $precioBeneficio = $ofertaAtributo->PrecioBeneficio;
                        $oferta->Titulo = $ofertaAtributo->NombreAtributo;
                    }

                    $ofertaSegmento = $this->getOfertaPuntosSegmentoTable()
                        ->getOfertaPuntosSegmentoByOfertaAll($oferta->id);

                    $campaniaId = 0;
                    foreach ($ofertaSegmento as $value) {
                        $segmento = $this->getSegmentoPuntosTable()->getSegmentosP($value->BNF2_Segmento_id);
                        $campaniaId = $segmento->BNF2_Campania_id;
                        break;
                    }

                    $campania = $this->getCampaniaPuntosTable()->getCampaniasP($campaniaId);

                    $fomData['id'] = $cupon->id;
                    $fomData['CodigoCupon'] = $codigoCupon;
                    $fomData['Titulo'] = "S./ " . $precioCupon . " por " . $oferta->Titulo;
                    $fomData['EmpresaProv'] = $empresaProv;
                    $fomData['Campania'] = $campania->NombreCampania;
                    $fomData['EstadoCampania'] = ($campania->EstadoCampania == 'Caducado') ? 'Caducada' : 'Activo';
                    $fomData['EstadoCupon'] = $cupon->EstadoCupon;
                    $fomData['PrecioCupon'] = $precioCupon;
                    $fomData['PrecioBeneficio'] = $precioBeneficio;
                    $fomData['PuntosUtilizados'] = $cupon->PuntosUtilizados;
                    $fomData['PrecioFinal'] = $precioCupon - $cupon->PuntosUtilizados;
                    $fomData['CondicionesUso'] = $oferta->CondicionesUso;

                    $fomData['comentario_uno'] = !empty($cuponLog->comentario_uno) ? $cuponLog->comentario_uno : "";
                    $fomData['comentario_dos'] =!empty($cuponLog->comentario_dos) ? $cuponLog->comentario_dos : "";

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
            $dataCupon = $this->getCuponPuntosTable()->getCuponPuntos($dataIdCupon);
            $cupon = $this->getCuponPuntosTable()
                ->searchCuponPuntos($dataCupon->CodigoCupon, $this->identity()->BNF_Empresa_id);
            if (is_object($cupon)) {
                $oferta = $this->getOfertaPuntosTable()->getOfertaPuntos($cupon->BNF2_Oferta_Puntos_id);
                $empresaProv = $this->getEmpresaTable()->getEmpresa($oferta->BNF_Empresa_id)->NombreComercial;
                $cuponLog = $this->getCuponPuntosLogTable()->getCuponPuntosLogByEstado($cupon->id, $cupon->EstadoCupon);

                if ($oferta->TipoPrecio == "Unico") {
                    $precioCupon = $oferta->PrecioVentaPublico;
                    $precioBeneficio = $oferta->PrecioBeneficio;
                } else {
                    $ofertaAtributo = $this->getOfertaPuntosAtributosTable()
                        ->getOfertaPuntosAtributos($cupon->BNF2_Oferta_Puntos_Atributos_id);
                    $precioCupon = $ofertaAtributo->PrecioVentaPublico;
                    $precioBeneficio = $ofertaAtributo->PrecioBeneficio;
                    $oferta->Titulo = $ofertaAtributo->NombreAtributo;
                }

                $ofertaSegmento = $this->getOfertaPuntosSegmentoTable()
                    ->getOfertaPuntosSegmentoByOfertaAll($oferta->id);

                $campaniaId = 0;
                foreach ($ofertaSegmento as $value) {
                    $segmento = $this->getSegmentoPuntosTable()->getSegmentosP($value->BNF2_Segmento_id);
                    $campaniaId = $segmento->BNF2_Campania_id;
                    break;
                }

                $campania = $this->getCampaniaPuntosTable()->getCampaniasP($campaniaId);

                $fomData['id'] = $cupon->id;
                $fomData['CodigoCupon'] = $dataCupon->CodigoCupon;
                $fomData['Titulo'] = "S./ " . $precioCupon . " por " . $oferta->Titulo;
                $fomData['EmpresaProv'] = $empresaProv;
                $fomData['Campania'] = $campania->NombreCampania;
                $fomData['EstadoCampania'] = ($campania->EstadoCampania == 'Caducado') ? 'Caducada' : 'Activo';
                $fomData['EstadoCupon'] = $cupon->EstadoCupon;
                $fomData['PrecioCupon'] = $precioCupon;
                $fomData['PrecioBeneficio'] = $precioBeneficio;
                $fomData['PuntosUtilizados'] = $cupon->PuntosUtilizados;
                $fomData['PrecioFinal'] = $precioCupon - $cupon->PuntosUtilizados;
                $fomData['CondicionesUso'] = $oferta->CondicionesUso;
                $fomData['FechaFinVigencia'] = date_format(date_create($cupon->FechaVigencia), 'Y-m-d');
                $fomData['Comentarios'] = !empty($cuponLog->Comentario) ? $cuponLog->Comentario : "";
                $fomData['comentario_uno'] = !empty($cuponLog->comentario_uno) ? $cuponLog->comentario_uno : "";
                $fomData['comentario_dos'] = !empty($cuponLog->comentario_dos) ? $cuponLog->comentario_dos : "";
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
                'puntos' => 'active',
                'cuponPuntos' => 'active',
                'editCuponPuntos' => 'active',
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
            $cupon = $this->getCuponPuntosTable()->getCuponPuntos($id);
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
                    $dataLog = $this->getCuponPuntosLogTable()->getCuponPuntosLogByCuponId($cupon->id);
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
                    $oferta = $this->getOfertaPuntosTable()->getOfertaPuntos($cupon->BNF2_Oferta_Puntos_id);

                    if ($oferta->TipoPrecio == "Unico") {
                        $fecha = $oferta->FechaVigencia;
                    } else {
                        $ofertaAtributo = $this->getOfertaPuntosAtributosTable()
                            ->getAllOfertaPuntosAtributos($cupon->BNF2_Oferta_Puntos_Atributos_id);
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
                    $oferta = $this->getOfertaPuntosTable()->getOfertaPuntos($cupon->BNF2_Oferta_Puntos_id);

                    if ($oferta->TipoPrecio == "Unico") {
                        $fecha = $oferta->FechaVigencia;
                    } else {
                        $ofertaAtributo = $this->getOfertaPuntosAtributosTable()
                            ->getOfertaPuntosAtributos($cupon->BNF2_Oferta_Puntos_Atributos_id);
                        $fecha = $ofertaAtributo->FechaVigencia;
                    }

                    $hoy = date_create('now');
                    $vigencia = date_create($fecha);
                    date_add($vigencia, date_interval_create_from_date_string($dias . ' days'));
                    $diferencia = date_diff($hoy, $vigencia);

                    if ($diferencia->format("%r%a") >= 0) {

                        $cuponPuntosAsignacion = $this->getCuponPuntosAsignacionTable()->getByCupon($cupon->id);

                        $asignacion = $this->getAsignacionTable()
                            ->getAsignacion($cuponPuntosAsignacion->BNF2_Asignacion_Puntos_id);

                        $asignacionEstadoLog = new AsignacionEstadoLog();
                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacion->id;
                        $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                        $asignacionEstadoLog->CantidadPuntos = (int)$asignacion->CantidadPuntos;
                        $asignacionEstadoLog->CantidadPuntosUsados = (int)$asignacion->CantidadPuntosUsados;
                        $asignacionEstadoLog->CantidadPuntosDisponibles = (int)$asignacion->CantidadPuntosDisponibles;
                        $asignacionEstadoLog->CantidadPuntosEliminados = (int)$asignacion->CantidadPuntosEliminados;
                        $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                        $asignacionEstadoLog->Operacion = $this::OPERACION_REDIMIR;
                        $asignacionEstadoLog->Puntos = $cupon->PuntosUtilizados;
                        $asignacionEstadoLog->Motivo = "Redimir Puntos";
                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                        $this->getCuponPuntosTable()->redimirCuponPuntos($cupon->id);

                        $cuponPuntosLog = new CuponPuntosLog();
                        $cuponPuntosLog->BNF2_Cupon_Puntos_id = $cupon->id;
                        $cuponPuntosLog->CodigoCupon = $cupon->CodigoCupon;
                        $cuponPuntosLog->EstadoCupon = "Redimido";
                        $cuponPuntosLog->BNF2_Oferta_Puntos_id = $cupon->BNF2_Oferta_Puntos_id;
                        $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $cupon->BNF2_Oferta_Puntos_Atributos_id;
                        $cuponPuntosLog->BNF_Cliente_id = $cupon->BNF_Cliente_id;
                        $cuponPuntosLog->BNF_Usuario_id = $this->identity()->id;
                        $cuponPuntosLog->Comentario = $comentario;
                        $this->getCuponPuntosLogTable()->saveCuponPuntosLog($cuponPuntosLog);

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
                        $this->getCuponPuntosTable()->saveCuponPuntos($cupon);
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
                    $cupon = $this->getCuponPuntosTable()->getCuponPuntos($id);
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
                        $cuponPuntosAsignacion = $this->getCuponPuntosAsignacionTable()->getByCupon($cupon->id);

                        $asignacion = $this->getAsignacionTable()
                            ->getAsignacion($cuponPuntosAsignacion->BNF2_Asignacion_Puntos_id);

                        $asignacionEstadoLog = new AsignacionEstadoLog();
                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacion->id;
                        $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                        $asignacionEstadoLog->CantidadPuntos = (int)$asignacion->CantidadPuntos;
                        $asignacionEstadoLog->CantidadPuntosUsados = (int)$asignacion->CantidadPuntosUsados;
                        $asignacionEstadoLog->CantidadPuntosDisponibles = (int)$asignacion->CantidadPuntosDisponibles;
                        $asignacionEstadoLog->CantidadPuntosEliminados = (int)$asignacion->CantidadPuntosEliminados;
                        $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                        $asignacionEstadoLog->Operacion = $this::OPERACION_REDIMIR;
                        $asignacionEstadoLog->Puntos = $cupon->PuntosUtilizados;
                        $asignacionEstadoLog->Motivo = "Redimir Puntos";
                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                        $this->getCuponPuntosTable()->redimirCuponPuntos($cupon->id);

                        $cuponPuntosLog = new CuponPuntosLog();
                        $cuponPuntosLog->BNF2_Cupon_Puntos_id = $cupon->id;
                        $cuponPuntosLog->CodigoCupon = $cupon->CodigoCupon;
                        $cuponPuntosLog->EstadoCupon = "Redimido";
                        $cuponPuntosLog->BNF2_Oferta_Puntos_id = $cupon->BNF2_Oferta_Puntos_id;
                        $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $cupon->BNF2_Oferta_Puntos_Atributos_id;
                        $cuponPuntosLog->BNF_Cliente_id = $cupon->BNF_Cliente_id;
                        $cuponPuntosLog->BNF_Usuario_id = $this->identity()->id;
                        $cuponPuntosLog->Comentario = $comentario;
                        $this->getCuponPuntosLogTable()->saveCuponPuntosLog($cuponPuntosLog);

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

            $comentario_uno = $post_data['comentario_uno'];
            $comentario_dos = $post_data['comentario_dos'];

            $estado = $post_data['EstadoCupon'];
            $csrf = new Csrf();
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    $cuponPuntos = $this->getCuponPuntosTable()->getCuponPuntos($id);
                    if ($estado == $this::ESTADO_CUPON_DESCARGADO) {
                        $this->getCuponPuntosTable()->generadoCuponPuntos($cuponPuntos->id);
                    } elseif ($estado == $this::ESTADO_CUPON_REDIMIDO) {
                        $this->getCuponPuntosTable()->redimirCuponPuntos($cuponPuntos->id);

                        $cuponPuntosAsignacion = $this->getCuponPuntosAsignacionTable()->getByCupon($cuponPuntos->id);

                        $asignacion = $this->getAsignacionTable()
                            ->getAsignacion($cuponPuntosAsignacion->BNF2_Asignacion_Puntos_id);

                        $asignacionEstadoLog = new AsignacionEstadoLog();
                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacion->id;
                        $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                        $asignacionEstadoLog->CantidadPuntos = (int)$asignacion->CantidadPuntos;
                        $asignacionEstadoLog->CantidadPuntosUsados = (int)$asignacion->CantidadPuntosUsados;
                        $asignacionEstadoLog->CantidadPuntosDisponibles = (int)$asignacion->CantidadPuntosDisponibles;
                        $asignacionEstadoLog->CantidadPuntosEliminados = (int)$asignacion->CantidadPuntosEliminados;
                        $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                        $asignacionEstadoLog->Operacion = $this::OPERACION_REDIMIR;
                        $asignacionEstadoLog->Puntos = $cuponPuntos->PuntosUtilizados;
                        $asignacionEstadoLog->Motivo = "Redimir Puntos";
                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);
                    } elseif ($estado == $this::ESTADO_CUPON_POR_PAGAR) {
                        $this->getCuponPuntosTable()->porPagarCuponPuntos($cuponPuntos->id);
                    } elseif ($estado == $this::ESTADO_CUPON_PAGADO) {
                        $this->getCuponPuntosTable()->pagadoCuponPuntos($cuponPuntos->id);
                    } elseif ($estado == $this::ESTADO_CUPON_STAND_BY) {
                        $this->getCuponPuntosTable()->standByCuponPuntos($cuponPuntos->id);
                    } elseif ($estado == $this::ESTADO_CUPON_ANULADO) {
                        $this->getCuponPuntosTable()->anularCuponPuntos($cuponPuntos->id);
                    } elseif ($estado == $this::ESTADO_CUPON_CADUCADO) {
                        $this->getCuponPuntosTable()->caducadoCuponPuntos($cuponPuntos->id);
                    }


                    $cuponPuntosLog = new CuponPuntosLog();
                    $cuponPuntosLog->BNF2_Cupon_Puntos_id = $cuponPuntos->id;
                    $cuponPuntosLog->CodigoCupon = $cuponPuntos->CodigoCupon;

                    $cuponPuntosLog->comentario_uno = $comentario_uno;
                    $cuponPuntosLog->comentario_dos = $comentario_dos;

                    $cuponPuntosLog->EstadoCupon = $estado;
                    $cuponPuntosLog->BNF2_Oferta_Puntos_id = $cuponPuntos->BNF2_Oferta_Puntos_id;
                    $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $cuponPuntos->BNF2_Oferta_Puntos_Atributos_id;
                    $cuponPuntosLog->BNF_Cliente_id = $cuponPuntos->BNF_Cliente_id;
                    $cuponPuntosLog->BNF_Usuario_id = $this->identity()->id;
                    $cuponPuntosLog->Comentario = $comentario;


                    $this->getCuponPuntosLogTable()->saveCuponPuntosLog($cuponPuntosLog);

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
                            $cuponPuntos = $this->getCuponPuntosTable()->getCuponPuntos($value);
                            $this->getCuponPuntosTable()->porPagarCuponPuntos($cuponPuntos->id);

                            $cuponPuntosLog = new CuponPuntosLog();
                            $cuponPuntosLog->BNF2_Cupon_Puntos_id = $cuponPuntos->id;
                            $cuponPuntosLog->CodigoCupon = $cuponPuntos->CodigoCupon;
                            $cuponPuntosLog->EstadoCupon = $this::ESTADO_CUPON_POR_PAGAR;
                            $cuponPuntosLog->BNF2_Oferta_Puntos_id = $cuponPuntos->BNF2_Oferta_Puntos_id;
                            $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $cuponPuntos->BNF2_Oferta_Puntos_Atributos_id;
                            $cuponPuntosLog->BNF_Cliente_id = $cuponPuntos->BNF_Cliente_id;
                            $cuponPuntosLog->BNF_Usuario_id = $this->identity()->id;
                            $cuponPuntosLog->Comentario = $comentario;
                            $this->getCuponPuntosLogTable()->saveCuponPuntosLog($cuponPuntosLog);
                        }
                    } elseif (count($pagado_check) > 0) {
                        foreach ($pagado_check as $value) {
                            $cuponPuntos = $this->getCuponPuntosTable()->getCuponPuntos($value);
                            $this->getCuponPuntosTable()->pagadoCuponPuntos($cuponPuntos->id);

                            $cuponPuntosLog = new CuponPuntosLog();
                            $cuponPuntosLog->BNF2_Cupon_Puntos_id = $cuponPuntos->id;
                            $cuponPuntosLog->CodigoCupon = $cuponPuntos->CodigoCupon;
                            $cuponPuntosLog->EstadoCupon = $this::ESTADO_CUPON_PAGADO;
                            $cuponPuntosLog->BNF2_Oferta_Puntos_id = $cuponPuntos->BNF2_Oferta_Puntos_id;
                            $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id = $cuponPuntos->BNF2_Oferta_Puntos_Atributos_id;
                            $cuponPuntosLog->BNF_Cliente_id = $cuponPuntos->BNF_Cliente_id;
                            $cuponPuntosLog->BNF_Usuario_id = $this->identity()->id;
                            $cuponPuntosLog->Comentario = $comentario;
                            $this->getCuponPuntosLogTable()->saveCuponPuntosLog($cuponPuntosLog);
                        }
                    }

                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => true,
                                'direccion' => '/cupon-puntos',
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


    public function exportreportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $empresaUsuario = $this->identity()->BNF_Empresa_id;

        $empresa = (!empty($empresaUsuario)) ? $empresaUsuario : (int)$this->params()->fromRoute('empresa', 0);

        $estado = $this->params()->fromRoute('estado', null);

        $desde = $this->params()->fromRoute('desde', null);

        $hasta = $this->params()->fromRoute('hasta', null);
        $title="";
        if ($estado == "Redimido") {
            $estado = ["Redimido"];
            $title="Redimidos";
        } else {
            $estado = ["Por Pagar","Pagado"];
            $title="Pagados";
        }

        $resultado = $this->getCuponPuntosTable()
            ->getListaCuponesPuntosPorEstado('UltimaActualizacion', 'DESC', $empresa, $estado, $desde, $hasta);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Información del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Cupones Puntos ".$title)
                ->setSubject("Cupones Puntos ".$title)
                ->setDescription("Documento Lista de Cupones Puntos ".$title)
                ->setKeywords("Beneficios.pe")
                ->setCategory("Cupones Puntos ".$title);

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:I' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

            #region Styles
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
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
            #endregion

            $objPHPExcel->getActiveSheet()->getStyle('A1:K' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Empresa Proveedora')
                ->setCellValue('C1', 'Campaña')
                ->setCellValue('D1', 'Oferta')
                ->setCellValue('E1', 'Código Cupon')
                ->setCellValue('F1', 'PVP')
                ->setCellValue('G1', 'PB')
                ->setCellValue('H1', 'Estado')
                ->setCellValue('I1', 'Ultima Actualización')
                ->setCellValue('J1', 'Campo 1')
                ->setCellValue('K1', 'Campo 2');
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
                    ->setCellValue('I' . $i, $registro->UltimaActualizacion)
                    ->setCellValue('J' . $i, $registro->ComentarioUno)
                    ->setCellValue('K' . $i, $registro->ComentarioDos);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="CuponesPuntos'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
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

        $resultado = $this->getCuponPuntosTable()
            ->getListaCuponesPuntos('UltimaActualizacion', 'DESC', $empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Información del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Cupones Puntos")
                ->setSubject("Cupones Puntos")
                ->setDescription("Documento Lista de Cupones Puntos")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Cupones Puntos");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:I' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

            #region Styles
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
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
            #endregion

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
        header('Content-Disposition: attachment;filename="CuponesPuntos.xlsx"');
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
                    $campanias = $this->getCampaniaPuntosTable()->getCampaniasPByEmpresa($id);
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
                    $ofertas = $this->getOfertaPuntosTable()->getAllOfertaPuntosByCampania($id);
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
