<?php

namespace Puntos\Controller;

use Puntos\Form\FormReporteOfertasTop;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class OfertasTopController extends AbstractActionController
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

    public function getSegmentoPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\SegmentosPTable');
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
            $form = new FormReporteOfertasTop('reporte', $empresa_value, $tipo_usuario);
        } else {
            $form = new FormReporteOfertasTop('reporte', $data[0]);
        }

        return new ViewModel(
            array(
                'form' => $form,
                'nombre_empresa' => $nombre_empresa,
                'reportespuntos' => 'active',
                'ofertastop' => 'active',
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
                ->setTitle("Reporte de Ofertas Top")
                ->setSubject("Reporte Ofertas Top")
                ->setDescription("Documento Reporte de Ofertas Top")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reporte Ofertas Top");

            #region Styles
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
            #endregion

            $resultado = $this->getOfertaPuntosTable()->getByEmpresaOrCampaniaOrSegmento(
                (int)$data['empresa'],
                (int)$data['campania'],
                (int)$data['segmento'],
                $data['estado']
            );

            $registros = count($resultado);
            $inicio = 8;

            #region General
            $empresa = $this->getEmpresaTable()->getEmpresa((int)$data['empresa']);
            $campania = $this->getCampaniaPuntosTable()->getCampaniasP((int)$data['campania']);
            $segmento = $this->getSegmentoPuntosTable()->getSegmentosP((int)$data['segmento']);

            $objPHPExcel->getActiveSheet()->setAutoFilter('A8:M' . ($registros + $inicio));
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A9:M' . ($inicio + $registros))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A8:M8')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('C9:C' . ($inicio + $registros))->applyFromArray($cellArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Reporte top ofertas puntos')
                ->mergeCells('A1:B1')
                ->setCellValue('A3', 'Empresa Cliente: ')
                ->setCellValue('A4', 'Campañas: ')
                ->setCellValue('A5', 'Segmentos: ')
                ->setCellValue('A6', 'Estado: ');

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B3', (!empty($empresa->NombreComercial) ? $empresa->NombreComercial : 'Todas'))
                ->setCellValue('B4', (!empty($campania->NombreCampania) ? $campania->NombreCampania : 'Todas'))
                ->setCellValue('B5', (!empty($segmento->NombreSegmento) ? $segmento->NombreSegmento : 'Todos'))
                ->setCellValue('B6', ($data['estado']) ?$data['estado'] :'Todos');

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A8', 'Empresa Cliente')
                ->setCellValue('B8', 'Campaña')
                ->setCellValue('C8', 'Segmento')
                ->setCellValue('D8', 'Empresa proveedora')
                ->setCellValue('E8', 'Oferta')
                ->setCellValue('F8', 'PB')
                ->setCellValue('G8', 'PVP')
                ->setCellValue('H8', 'Vigencia cupones')
                ->setCellValue('I8', 'Vigencia Campaña')
                ->setCellValue('J8', 'Stock')
                ->setCellValue('K8', 'Rubro')
                ->setCellValue('L8', 'Descargas')
                ->setCellValue('M8', 'Redenciones');
            #endregion

            #region Data
            if ($registros > 0) {
                $i = $inicio + 1;

                foreach ($resultado as $registro) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, $registro->Empresa)
                        ->setCellValue('B' . $i, $registro->Campania)
                        ->setCellValue('C' . $i, $registro->Segmentos)
                        ->setCellValue('D' . $i, $registro->BNF_Empresa_id)
                        ->setCellValue('E' . $i, $registro->Titulo)
                        ->setCellValue('F' . $i, $registro->PrecioBeneficio)
                        ->setCellValue('G' . $i, $registro->PrecioVentaPublico)
                        ->setCellValue('H' . $i, $registro->FechaVigencia)
                        ->setCellValue('I' . $i, $registro->VigenciaCampania)
                        ->setCellValue('J' . $i, (int)$registro->Stock)
                        ->setCellValue('K' . $i, $registro->Rubro)
                        ->setCellValue('L' . $i, (int)$registro->Descargas)
                        ->setCellValue('M' . $i, (int)$registro->Redimidas);
                    $i++;
                }
            }
            #endregion

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="ReporteOfertasTop.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
        return new ViewModel();
    }


}

