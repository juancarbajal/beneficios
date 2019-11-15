<?php

namespace Reportes\Controller;

use Reportes\Form\PeriodoForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class OfertaDescargasController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresas = array();
        $nombre_empresa = null;
        $getEmpresaTable = $this->serviceLocator->get('Empresa\Model\EmpresaTable');
        $dataEmpresas = $getEmpresaTable->getEmpresaCli();
        foreach ($dataEmpresas as $e) {
            $empresas[$e->id] = $e->NombreComercial . " (" . $e->RazonSocial . ") - " . $e->Ruc;
        }

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $empresas[$empresa_value];
            $form = new PeriodoForm('periodo', $empresa_value, $tipo_usuario);
        } else {
            $form = new PeriodoForm('periodo', $empresas);
        }

        return new ViewModel(
            array(
                "reportes" => 'active',
                "reporteoferdes" => 'active',
                "form" => $form,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
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
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        );

        $ofetaTable = $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $fechaInicio_defecto = '2016-01-01';
            $fechaFin_defecto = date('Y-m-d');
            $fechaInicio = ($request->getPost()->FechaInicio2 == '') ? $fechaInicio_defecto : $request->getPost()->FechaInicio2;
            $fechaFin = ($request->getPost()->FechaFin2 == '') ?
                $fechaFin_defecto : $request->getPost()->FechaFin2;

            $data = $ofetaTable->getCantDescargasPorOferta($fechaInicio, $fechaFin);

            //var_dump($data);exit;
            $objPHPExcel = new PHPExcel();
            if (count($data) > 0) {
                //Informacion del excel
                $objPHPExcel->
                getProperties()
                    ->setCreator("Beneficios.pe")
                    ->setLastModifiedBy("Beneficios.pe")
                    ->setSubject("Reporte Descargas por Oferta")
                    ->setDescription("Reporte Descargas por Oferta")
                    ->setKeywords("Beneficios.pe")
                    ->setCategory("Reporte Descargas por Oferta");
                $objPHPExcel->getActiveSheet()->setTitle("Reporte Descargas por Oferta");

                //desactiva cuadricula
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

                $objPHPExcel->getActiveSheet()->setAutoFilter('A6:D6');
                $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('A7:D' . (count($data) + 6))->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A3:C3')->applyFromArray($styleArray2);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(0, 2, 'Reporte Top Ofertas descargadas')
                    ->setCellValueByColumnAndRow(0, 3, 'Periodo')
                    ->setCellValueByColumnAndRow(1, 3, $fechaInicio)
                    ->setCellValueByColumnAndRow(2, 3, $fechaFin);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(0, 6, 'Empresa Proveedora')
                    ->setCellValueByColumnAndRow(1, 6, 'DatoBeneficio')
                    ->setCellValueByColumnAndRow(2, 6, 'Titulo de Oferta')
                    ->setCellValueByColumnAndRow(3, 6, 'Descargas');

                $fila = 7;
                foreach ($data as $value) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow(0, $fila, $value->NombreComercial)
                        ->setCellValueByColumnAndRow(1, $fila, $value->DatoBeneficio)
                        ->setCellValueByColumnAndRow(2, $fila, $value->Titulo)
                        ->setCellValueByColumnAndRow(3, $fila, $value->Descargas);
                    $fila++;
                }

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="Reporte_Descargas_Oferta.xlsx"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }
        }
        exit;
    }
}

