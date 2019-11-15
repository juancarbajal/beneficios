<?php

namespace Puntos\Controller;

use Puntos\Form\FormReporteProveedorCampanias;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class ProveedorCampaniasController extends AbstractActionController
{
    #region ObjectTables
    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosTable');
    }

    public function getCampaniaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    #endregion

    public function inicializacionBusqueda()
    {
        $dataEmpCli = array();
        $filterEmpCli = array();

        try {
            foreach ($this->getCampaniaPuntosTable()->getEmpresasProveedora() as $empresa) {
                $dataEmpCli[$empresa->id] = $empresa->Empresa;
                $filterEmpCli[$empresa->id] = [$empresa->id];
            }
        } catch (\Exception $ex) {
            $dataEmpCli = array();
        }

        $formulario['emp'] = $dataEmpCli;
        $filtro['emp'] = array_keys($filterEmpCli);
        return array($formulario, $filtro);
    }

    public function indexAction()
    {
        $data = $this->inicializacionBusqueda();
        if (!$this->identity()) {
            $this->redirect()->toUrl('/login');
        }
        $empresa = $this->identity()->BNF_Empresa_id;
        if ($this->identity()->TipoUsuario == "proveedor") {
            $type = "proveedor";
            $nombre = $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa)->NombreComercial;
        } else {
            $type = "admin";
            $nombre = "";
        }
        $form = new FormReporteProveedorCampanias('reporte', $data[0], $type);
        $form->get('empresa')->setAttribute('value', $empresa);
        return new ViewModel(
            array(
                'form' => $form,
                'type' => $type,
                'nombre' => $nombre,
                'reportespuntos' => 'active',
                'reportesprocamp' => 'active',
            )
        );
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $request = $this->getRequest();
        $tipo = $this->identity()->TipoUsuario;
        if ($request->isPost()) {
            $data = $request->getPost();

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte de Proveedor Campañas")
                ->setSubject("Reporte Proveedor Campañas")
                ->setDescription("Documento Reporte de Proveedor Campañas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reporte Proveedor Campañas");

            $tittleStyleArray = array(
                'font' => array(
                    'bold' => true,
                )
            );

            $cellArray = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                )
            );

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
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
                ),
            );

            $resultado = $this->getOfertaPuntosTable()
                ->getByEmpresaOrCampania((int)$data['empresa'], (int)@$data['campania']);

            $registros = count($resultado);
            $inicio = 6;

            #region General
            $empresa = $this->getEmpresaTable()->getEmpresa((int)$data['empresa']);
            $campania = $this->getCampaniaPuntosTable()->getCampaniasP((int)$data['campania']);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);
            $objPHPExcel->getActiveSheet()->getStyle('C7:C' . ($inicio + $registros))->applyFromArray($cellArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Reporte proveedor campañas')
                ->mergeCells('A1:B1')
                ->setCellValue('A3', 'Empresa Proveedor: ')
                ->setCellValue('A4', 'Campañas: ');

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B3', (!empty($empresa->NombreComercial) ? $empresa->NombreComercial : 'Todas'))
                ->setCellValue('B4', (!empty($campania->NombreCampania) ? $campania->NombreCampania : 'Todas'));

            if ($tipo != 'proveedor') {
                $objPHPExcel->getActiveSheet()->setAutoFilter('A6:F' . ($registros + $inicio));
                $objPHPExcel->getActiveSheet()->getStyle('A7:F' . ($inicio + $registros))->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A6:F6')->applyFromArray($styleArray);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A6', 'Empresa Proveedor');
            } else {
                $objPHPExcel->getActiveSheet()->setAutoFilter('B6:F' . ($registros + $inicio));
                $objPHPExcel->getActiveSheet()->getStyle('B7:F' . ($inicio + $registros))->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('B6:F6')->applyFromArray($styleArray);
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B6', 'Campaña')
                ->setCellValue('C6', 'Oferta')
                ->setCellValue('D6', 'PB')
                ->setCellValue('E6', 'Redimidos')
                ->setCellValue('F6', 'Ingresos');
            #endregion

            #region Data
            if ($registros > 0) {
                $i = $inicio + 1;

                foreach ($resultado as $registro) {
                    if ($tipo != 'proveedor') {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $registro->BNF_Empresa_id);
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $registro->Campania)
                        ->setCellValue('C' . $i, $registro->Titulo)
                        ->setCellValue('D' . $i, $registro->PrecioBeneficio)
                        ->setCellValue('E' . $i, (int)$registro->Redimidas)
                        ->setCellValue('F' . $i, $registro->PrecioBeneficio * (int)$registro->Redimidas);
                    $i++;
                }
            }
            #endregion

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="ReporteProveedorCampañas.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
        return new ViewModel();
    }

}

