<?php

namespace Puntos\Controller;

use Puntos\Form\FormReporteIngresosGlobales;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class IngresosGlobalesController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;

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
            foreach ($this->getCampaniaPuntosTable()->getEmpresasCliente() as $empresa) {
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
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $data = $this->inicializacionBusqueda();
        $nombre_empresa = "";

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $data[0]['emp'][$empresa_value];
            $form = new FormReporteIngresosGlobales('reporte', $empresa_value, $tipo_usuario);
        } else {
            $form = new FormReporteIngresosGlobales('reporte', $data[0]);
        }

        return new ViewModel(
            array(
                'form' => $form,
                'nombre_empresa' => $nombre_empresa,
                'reportespuntos' => 'active',
                'ingreglob' => 'active',
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
        if ($request->isPost()) {
            $data = $request->getPost();

            $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
            $empresa_value = $this->identity()->BNF_Empresa_id;
            if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                $data['empresa'] = $empresa_value;
            }

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte de Ingresos Globales")
                ->setSubject("Reporte Ingresos Globales")
                ->setDescription("Documento Reporte de Ingresos Globales")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reporte Ingresos Globales");

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

            $resultado = $this->getOfertaPuntosTable()->getByEmpresaOrCampaniaOrRango(
                (int)$data['empresa'],
                (int)$data['campania'],
                $data['FechaInicio'],
                $data['FechaFin']
            );

            $registros = count($resultado);
            $inicio = 7;

            #region General
            $empresa = $this->getEmpresaTable()->getEmpresa((int)$data['empresa']);
            $campania = $this->getCampaniaPuntosTable()->getCampaniasP((int)$data['campania']);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);


            $objPHPExcel->getActiveSheet()->getStyle('A7:B7')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('C11:C' . ($inicio + $registros))->applyFromArray($cellArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Reporte de ingresos globales')
                ->mergeCells('A1:B1')
                ->setCellValue('A3', 'Empresa Cliente: ')
                ->setCellValue('A4', 'Campañas: ')
                ->setCellValue('A5', 'Periodo de redención: ');

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B3', (!empty($empresa->NombreComercial) ? $empresa->NombreComercial : 'Todas'))
                ->setCellValue('B4', (!empty($campania->NombreCampania) ? $campania->NombreCampania : 'Todas'))
                ->setCellValue('B5', $data['FechaInicio'] . ' - ' . $data['FechaFin']);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A7', 'Campaña')
                ->setCellValue('B7', 'Utilidad');

            #endregion

            #region Data
            if ($registros > 0) {
                $i = $inicio + 1;

                $utilidad = array();
                foreach ($resultado as $value) {
                    $calculo = $value->Redimidas * ($value->PrecioVentaPublico - $value->PrecioBeneficio);
                    @$utilidad[$value->Campania] += $calculo;
                }

                foreach ($utilidad as $key => $value) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $key)
                        ->setCellValue('B' . $i, (int)$value);
                    $i++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('A7:B' . ($i - 1))->applyFromArray($styleArray2);

                $i++;
                $posOfertas = $i;
                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->setAutoFilter('A' . $i . ':E' . $i);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, 'Oferta')
                    ->setCellValue('B' . $i, 'PVP')
                    ->setCellValue('C' . $i, 'PB')
                    ->setCellValue('D' . $i, 'Redimidas')
                    ->setCellValue('E' . $i, 'Utilidad');
                $i++;
                foreach ($resultado as $registro) {
                    $calculo = $registro->Redimidas * ($registro->PrecioVentaPublico - $registro->PrecioBeneficio);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $registro->Titulo)
                        ->setCellValue('B' . $i, $registro->PrecioVentaPublico)
                        ->setCellValue('C' . $i, $registro->PrecioBeneficio)
                        ->setCellValue('D' . $i, $registro->Redimidas)
                        ->setCellValue('E' . $i, (int)$calculo);
                    $i++;
                }
                $objPHPExcel->getActiveSheet()->getStyle('A' . $posOfertas . ':E' . ($i - 1))->applyFromArray($styleArray2);
            }
            #endregion

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="IngresosGlobales.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
        return new ViewModel();
    }


}

