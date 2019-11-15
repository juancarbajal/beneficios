<?php

namespace Reportes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class OfertasPublicadasController extends AbstractActionController
{
    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $getOfertas = $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
        $ofertas = $getOfertas->getOfertasPublicadas();

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
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        );

        if (count($ofertas)) {
            $objPHPExcel = new PHPExcel();

            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte de Ofertas Publicadas")
                ->setSubject("Reporte de Ofertas Publicadas")
                ->setDescription("Reporte de Ofertas Publicadas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reporte de Ofertas Publicadas");

            //desactiva cuadricula
            $objPHPExcel->getActiveSheet()->setShowGridlines(false);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

            $objPHPExcel->getActiveSheet()->getStyle('B4:F' . (count($ofertas) + 4))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->applyFromArray($styleArray);

            $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->applyFromArray($styleArray2);
            ///

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B2', 'REPORTE DE OFERTAS PUBLICADAS')
                ->setCellValue('B4', 'Nombre Comercial')
                ->setCellValue('C4', 'Categoría')
                ->setCellValue('D4', 'Titulo Corto')
                ->setCellValue('E4', 'Fecha Fin de Publicación')
                ->setCellValue('F4', 'Stock');

            $row = 5;
            foreach ($ofertas as $data) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(1, $row, $data->NombreComercial)
                    ->setCellValueByColumnAndRow(2, $row, $data->Categoria)
                    ->setCellValueByColumnAndRow(3, $row, $data->TituloCorto)
                    ->setCellValueByColumnAndRow(4, $row, $data->FechaFinPublicacion)
                    ->setCellValueByColumnAndRow(5, $row, $data->Stock);
                $row++;
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Ofertas_Publicadas.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }

        return new ViewModel();
    }
}
