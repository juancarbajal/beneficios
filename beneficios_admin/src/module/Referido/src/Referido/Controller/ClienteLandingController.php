<?php

namespace Referido\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Referido\Form\FinalUserSearchForm;
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

class ClienteLandingController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;
    const TIPO_DNI = 1;
    const TIPO_PASAPORTE = 2;
    const TIPO_OTROS = 3;

    #region ObjectTables
    public function getReferidoTable()
    {
        return $this->serviceLocator->get('Referido\Model\Table\ReferidoTable');
    }

    public function getClienteLandingTable()
    {
        return $this->serviceLocator->get('Referido\Model\Table\ClienteLandingTable');
    }

    #endregion

    public function indexAction()
    {
        try {
            $nombre_empresa = null;
            $searchClient = null;
            $searchDateIni = null;
            $searchDateEnd = null;
            $stateDateIni = false;
            $stateDateEnd = false;

            $identity = $this->identity();
            if (!$identity) {
                return $this->redirect()->toUrl('/login');
            }

            $busqueda = array(
                'Fecha' => 'Creado',
                'Nombre' => 'Nombres_Apellidos',
                'Telefonos' => 'Telefonos',
                'Documento' => 'Documento',
                'Email' => 'Email',
                'Especialista' => 'Especialista',
                'FechaAsignacion' => 'FechaAsignacion',
                'PuntosAsignados' => 'PuntosAsignados',
            );

            $form = new FinalUserSearchForm('buscar');

            $request = $this->getRequest();
            if ($request->isPost()) {
                $searchClient = $request->getPost()->cliente ? trim($request->getPost()->cliente) : null;
                $searchDateIni = $request->getPost()->fecha_ini ? $request->getPost()->fecha_ini : null;
                $searchDateEnd = $request->getPost()->fecha_fin ? $request->getPost()->fecha_fin : null;
            } else {
                $searchClient = $this->params()->fromRoute('cliente') ? trim($this->params()->fromRoute('cliente')) : null;
                $searchDateIni = $this->params()->fromRoute('fecha_ini') ? $this->params()->fromRoute('fecha_ini') : null;
                $searchDateEnd = $this->params()->fromRoute('fecha_fin') ? $this->params()->fromRoute('fecha_fin') : null;
            }

            $form->setData(
                array(
                    'cliente' => trim($searchClient),
                    'fecha_ini' => $searchDateIni,
                    'fecha_fin' => $searchDateEnd
                )
            );

            $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
            $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
            $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;

            if (array_key_exists($order_by, $busqueda)) {
                $order_by_o = $order_by;
                $order_by = $busqueda[$order_by];
            } else {
                $order_by_o = 'id';
                $order_by = 'id';
            }

            if (!$searchDateIni and $searchDateEnd) {
                $searchDateIni = date("1990-01-01");
                $stateDateIni = true;
            }

            if ($searchDateIni and !$searchDateEnd) {
                $searchDateEnd = date("Y-m-d");
                $stateDateEnd = true;
            }

            $itemsPerPage = 10;
            $lista_clientes = array();

            $clients = $this->getClienteLandingTable()
                ->getAllClients($searchClient, $searchDateIni, $searchDateEnd, $order_by, $order);

            $paginator = new Paginator(new paginatorIterator($clients));
            $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(7);

            foreach ($paginator as $value) {
                array_push(
                    $lista_clientes,
                    array(
                        'id' => $value->id,
                        'Fecha' => date('Y-m-d', strtotime($value->Creado)),
                        'Nombre' => $value->Nombres_Apellidos,
                        'Documento' => $value->Documento,
                        'Telefonos' => $value->Telefonos,
                        'Email' => $value->Email,
                        'Especialista' => $value->Especialista,
                        'FechaAsignacion' => $value->FechaAsignacion ? date('Y-m-d', strtotime($value->FechaAsignacion)) : null,
                        'PuntosAsignados' => $value->PuntosAsignados
                    )
                );
            }

            if ($stateDateIni) {
                $searchDateIni = null;
            }

            if ($stateDateEnd) {
                $searchDateEnd = null;
            }

            if (strcasecmp($order, "desc") == 0) {
                $order = "asc";
            } else {
                $order = "desc";
            }

            return new ViewModel(
                array(
                    'referido' => 'active',
                    'ulistarcliente' => 'active',
                    'form' => $form,
                    'lista_clientes' => $lista_clientes,
                    'clientes' => $paginator,
                    'order_by' => $order_by_o,
                    'order' => $order,
                    'searchClient' => $searchClient,
                    'searchDateIni' => $searchDateIni,
                    'searchDateEnd' => $searchDateEnd,
                )
            );
        }catch (\Exception $ex)
        {var_dump($ex);exit;}
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $searchClient = $this->params()->fromRoute('cliente') ? trim($this->params()->fromRoute('cliente')) : null;
        $searchDateIni = $this->params()->fromRoute('fecha_ini') ? $this->params()->fromRoute('fecha_ini') : null;
        $searchDateEnd = $this->params()->fromRoute('fecha_fin') ? $this->params()->fromRoute('fecha_fin') : null;

        if (!$searchDateIni) {
            $searchDateIni = date("1990-01-01");
        }

        if (!$searchDateEnd) {
            $searchDateEnd = date("Y-m-d");
        }

        $resultado = $clients = $this->getClienteLandingTable()->getAllClients($searchClient, $searchDateIni, $searchDateEnd);

        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Clientes/Colaboradores")
                ->setSubject("Clientes Colaboradores")
                ->setDescription("Documento listando los Clientes/Colaboradores")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Clientes/Colaboradores");

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
                ->setCellValue('B1', 'Fecha de Creacion')
                ->setCellValue('C1', 'Fecha de Asignacion')
                ->setCellValue('D1', 'Documento')
                ->setCellValue('E1', 'Puntos Asignados')
                ->setCellValue('F1', 'Nombre y Apellidos')
                ->setCellValue('G1', 'Telefono')
                ->setCellValue('H1', 'Correo')
                ->setCellValue('I1', 'Especialista');

            $i = 2;
            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, date('Y-m-d', strtotime($registro->Creado)))
                    ->setCellValue('C' . $i, $registro->FechaAsignacion
                        ? date('Y-m-d', strtotime($registro->FechaAsignacion)) : '')
                    ->setCellValue('D' . $i, $registro->Documento)
                    ->setCellValue('E' . $i, $registro->PuntosAsignados)
                    ->setCellValue('F' . $i, $registro->Nombres_Apellidos)
                    ->setCellValue('G' . $i, $registro->Telefonos)
                    ->setCellValue('H' . $i, $registro->Email)
                    ->setCellValue('I' . $i, $registro->Especialista);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Clientes_Colaboradores.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function getReferidosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $referido = "";
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) && $csrf->verifyToken($post_data['csrf'])) {
                    $result = $this->getClienteLandingTable()->getCliente($id);
                    if ($result) {
                        $referido = $this->getReferidoTable()->getReferidoByCliente($id)->toArray();
                        $state = true;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'referido' => $referido,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

}
