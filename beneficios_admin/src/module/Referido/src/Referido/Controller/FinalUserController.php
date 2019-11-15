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

class FinalUserController extends AbstractActionController
{
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
            'Fecha' => 'Fecha_referencia',
            'Nombre' => 'Nombres_Apellidos',
            'Telefonos' => 'Telefonos',
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

        if (!$searchDateIni) {
            $searchDateIni = date("1990-01-01");
            $stateDateIni = true;
        }

        if (!$searchDateEnd) {
            $searchDateEnd = date("Y-m-d");
            $stateDateEnd = true;
        }

        $itemsPerPage = 10;
        $lista_clientes = array();

        $clients = $this->getReferidoTable()
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
                    'Nombre' => $value->Nombres_Apellidos,
                    'Telefonos' => $value->Telefonos,
                    'Fecha' => date('Y-m-d', strtotime($value->Fecha_referencia))
                )
            );
        }

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        if ($stateDateIni) {
            $searchDateIni = null;
        }

        if ($stateDateEnd) {
            $searchDateEnd = null;
        }

        return new ViewModel(
            array(
                'referido' => 'active',
                'ulistar' => 'active',
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

        $resultado = $clients = $this->getReferidoTable()
            ->getAllClients($searchClient, $searchDateIni, $searchDateEnd);

        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Referidos")
                ->setSubject("Referidos")
                ->setDescription("Documento listando los Referidos")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Referidos");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:E' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:E' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Nombre y Apellidos')
                ->setCellValue('C1', 'Telefono')
                ->setCellValue('D1', 'Fecha de Referencia')
                ->setCellValue('E1', 'Referido por');

            $i = 2;
            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Nombres_Apellidos)
                    ->setCellValue('C' . $i, $registro->Telefonos)
                    ->setCellValue('D' . $i, date('Y-m-d', strtotime($registro->Fecha_referencia)))
                    ->setCellValue('E' . $i, $registro->ReferidoPor);
                $i++;
            }

            $objPHPExcel->getActiveSheet()->getStyle('E1:E'. ($registros + 1))->getAlignment()->setWrapText(true);
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Referidos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function getReferidoPorAction()
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
                    $result = $this->getReferidoTable()->getReferido($id);
                    if ($result) {
                        $referido = $this->getClienteLandingTable()->getCliente($result->cliente_id);

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
