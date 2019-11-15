<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/08/16
 * Time: 11:45 AM
 */

namespace Puntos\Controller;

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

class ReportePagadosController extends AbstractActionController
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
            foreach ($this->getEmpresaTable()->getEmpresaProvReport() as $empresa) {
                $dataEmpCli[$empresa->id] = $empresa->NombreComercial.' - '.$empresa->RazonSocial.' - '.$empresa->Ruc;
                $filterEmpCli[$empresa->id] = [$empresa->id];
            }

        } catch (\Exception $ex) {
            $dataEmpCli = array();
        }

        $formulario['prov'] = $dataEmpCli;
        $filtro['prov'] = array_keys($filterEmpCli);
        return array($formulario, $filtro);
    }


    public function indexAction()
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
            //$estado = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
            $desde = $this->params()->fromRoute('q5') ? $this->params()->fromRoute('q5') : null;
            $hasta = $this->params()->fromRoute('q6') ? $this->params()->fromRoute('q6') : null;
            $estado = $estadoCupon;
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
                'reportespuntos' => 'active',
                'reportespagados' => 'active',
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
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
                ->setCellValue('A1', 'Fecha')
                ->setCellValue('B1', 'Empresa proveedora')
                ->setCellValue('C1', 'Cupon')
                ->setCellValue('D1', 'Campo 1')
                ->setCellValue('E1', 'Campo 2')
                ->setCellValue('F1', 'Oferta')
                ->setCellValue('G1', 'Precio Publicado')
                ->setCellValue('H1', 'Monto a recibir')
                ->setCellValue('I1', 'Estado');
            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->UltimaActualizacion)
                    ->setCellValue('B' . $i, $registro->Empresa)
                    ->setCellValue('C' . $i, $registro->CodigoCupon)
                    ->setCellValue('D' . $i, $registro->ComentarioUno)
                    ->setCellValue('E' . $i, $registro->ComentarioDos)
                    ->setCellValue('F' . $i,  "S/. " . $registro->PrecioVentaPublico . " por " . $registro->Oferta)
                    ->setCellValue('G' . $i, $registro->PrecioVentaPublico)
                    ->setCellValue('H' . $i, $registro->PrecioBeneficio)
                    ->setCellValue('I' . $i, $registro->EstadoCupon);
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
