<?php

namespace Oferta\Controller;

use Oferta\Form\BuscarOfertasConsumidasForm;
use Oferta\Model\Data\BuscarOfertaConsumidaData;
use Oferta\Model\Filter\BuscarOfertaConsumidaFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class OfertaConsumidasController extends AbstractActionController
{
    #region ObjectTables
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }

    #endregion

    public function indexAction()
    {
        $mensaje = null;
        $titulo = null;
        $estado = null;
        $fechainicio = null;
        $fechafin = null;

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $datos = new BuscarOfertaConsumidaData($this, $this->identity()->BNF_Empresa_id);

        $form = new BuscarOfertasConsumidasForm('paquete-oferta', $datos->getFormData());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $this->getRequest()->getPost()->toArray();
            $validate = new BuscarOfertaConsumidaFilter();

            $form->setInputFilter(
                $validate->getInputFilter($datos->getFilterData(), $post)
            );

            $form->setData($post);
            if ($form->isValid()) {
                $titulo = $request->getPost()->Titulo ? $request->getPost()->Titulo : null;
                $estado = $request->getPost()->Estado ? $request->getPost()->Estado : null;
                $fechainicio = $request->getPost()->FechaInicio ? $request->getPost()->FechaInicio : null;
                $fechafin = $request->getPost()->FechaFin ? $request->getPost()->FechaFin : null;
            }
        } else {
            $titulo = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $estado = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $fechainicio = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
            $fechafin = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
        }
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (!$fechainicio) {
            $fechainicio = date("1990-01-01");
        }

        if (!$fechafin) {
            $fechafin = date("Y-m-d");
        }

        $ofertasConsumidas = $this->getOfertaTable()->getOfertasConsumidas(
            $fechainicio,
            $fechafin,
            $titulo,
            $estado,
            $this->identity()->BNF_Empresa_id
        );

        $paginator = new Paginator(new paginatorIterator($ofertasConsumidas));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paqoff' => 'active',
                'mensaje' => $mensaje,
                'form' => $form,
                'ofertas' => $paginator,
                'p' => $page,
                'q1' => $titulo,
                'q2' => $estado,
                'q3' => $fechainicio,
                'q4' => $fechafin,
            )
        );
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $titulo = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
        $estado = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
        $fechainicio = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
        $fechafin = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
        $empresa_id = (int)$this->identity()->BNF_Empresa_id;
        $type_user = $this->identity()->TipoUsuario;
        if ($type_user == 'super' && $empresa_id != 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        } elseif ($type_user == 'proveedor' && $empresa_id == 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if ($fechafin == null and $fechainicio == null) {
            $ofertasConsumidas = $this->getOfertaTable()->getOfertasConsumidas(
                null,
                null,
                $titulo,
                $estado,
                $empresa_id
            );
        } elseif ($fechafin == null) {
            $ofertasConsumidas = $this->getOfertaTable()->getOfertasConsumidas(
                $fechainicio,
                date("Y-m-d"),
                $titulo,
                $estado,
                $empresa_id
            );
        } elseif ($fechainicio == null) {
            $ofertasConsumidas = $this->getOfertaTable()->getOfertasConsumidas(
                date("1990-01-01"),
                $fechafin,
                $titulo,
                $estado,
                $empresa_id
            );
        } else {
            $ofertasConsumidas = $this->getOfertaTable()->getOfertasConsumidas(
                $fechainicio,
                $fechafin,
                $titulo,
                $estado,
                $empresa_id
            );
        }
        $registros = count($ofertasConsumidas);
        $objPHPExcel = new PHPExcel();

        if ($registros > 0) {
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Ofertas Consumidas")
                ->setSubject("Ofertas")
                ->setDescription("Documento de reporte de Ofertas Consumidas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Ofertas");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:G' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:G' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Titulo')
                ->setCellValue('B1', 'Fecha de Inicio de Publicación')
                ->setCellValue('C1', 'Estado')
                ->setCellValue('D1', 'Asignaciones')
                ->setCellValue('E1', 'Descargados')
                ->setCellValue('F1', 'No Utilizados')
                ->setCellValue('G1', 'Redimidos');

            $i = 2;

            foreach ($ofertasConsumidas as $registro) {
                if ($registro->BNF_BolsaTotal_TipoPaquete_id == 1) {
                    $registro->Stock = (($registro->Stock) != 1)
                        ? ((int)$registro->Stock) . ' Descargas' : '1 Descarga';
                } elseif ($registro->BNF_BolsaTotal_TipoPaquete_id == 2) {
                    $registro->Stock = (($registro->Stock) != 1)
                        ? ((int)$registro->Stock) . ' Días' : '1 Día';
                } elseif ($registro->BNF_BolsaTotal_TipoPaquete_id == 3) {
                    $registro->Stock = (($registro->Stock) != 1)
                        ? ((int)$registro->Stock) . ' Leads' : '1 Lead';
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->Titulo)
                    ->setCellValue('B' . $i, $registro->FechaInicioPublicacion)
                    ->setCellValue('C' . $i, $registro->Estado)
                    ->setCellValue('D' . $i, $registro->Stock)
                    ->setCellValue('E' . $i, ((int)$registro->Descargados > 0) ?
                        (int)$registro->Descargados : '0')
                    ->setCellValue('F' . $i, ((int)$registro->NoUtilizados > 0) ?
                        (int)$registro->NoUtilizados : '0')
                    ->setCellValue('G' . $i, ((int)$registro->Redimidos > 0) ?
                        (int)$registro->Redimidos : '0');
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="OfertasConsumidas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
