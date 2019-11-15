<?php

namespace Paquete\Controller;

use Paquete\Form\BuscarPaquetesComprados;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class PaquetesCompradosController extends AbstractActionController
{
    #region ObjectTables
    public function getPaqueteTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaqueteTable');
    }

    public function getPaqueteEmpresaProveedorTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaqueteEmpresaProveedorTable');
    }
    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $mensaje = null;
        $valores = array();

        try {
            $ofertas = $this->getPaqueteTable()->getPaqueteNombre();
            foreach ($ofertas as $dato) {
                $valores[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            $valores = array();
        }

        $form = new BuscarPaquetesComprados($valores);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $paquete = $request->getPost()->Paquete ? $request->getPost()->Paquete : null;
            $factura = $request->getPost()->Factura ? $request->getPost()->Factura : null;
            $fechainicio = $request->getPost()->FechaInicio ? $request->getPost()->FechaInicio : null;
            $fechafin = $request->getPost()->FechaFin ? $request->getPost()->FechaFin : null;
        } else {
            $paquete = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $factura = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $fechainicio = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
            $fechafin = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
        }

        $form->get('Paquete')->setValue($paquete);
        $form->get('Factura')->setValue($factura);
        $form->get('FechaInicio')->setValue($fechainicio);
        $form->get('FechaFin')->setValue($fechafin);

        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if ($fechafin == null and $fechainicio == null) {
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                null,
                null,
                $paquete,
                $factura,
                $this->identity()->BNF_Empresa_id
            );
        } elseif ($fechafin == null) {
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                $fechainicio,
                date("Y-m-d"),
                $paquete,
                $factura,
                $this->identity()->BNF_Empresa_id
            );
        } elseif ($fechainicio == null) {
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                date("1990-01-01"),
                $fechafin,
                $paquete,
                $factura,
                $this->identity()->BNF_Empresa_id
            );
        } else {
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                $fechainicio,
                $fechafin,
                $paquete,
                $factura,
                $this->identity()->BNF_Empresa_id
            );
        }

        $paginator = new Paginator(new paginatorIterator($paquetes));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paqcomp' => 'active',
                'mensaje' => $mensaje,
                'form' => $form,
                'paquetes' => $paginator,
                'p' => $page,
                'q1' => $paquete,
                'q2' => $factura,
                'q3' => $fechainicio,
                'q4' => $fechafin,
            )
        );
    }

    public function getdetalleAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $data = array();

        try {
            $id = $this->getRequest()->getPost('id');
            $paquete = $this->getPaqueteEmpresaProveedorTable()->getPaqueteProv($id);
            $data['NombrePaquete'] = $paquete->NombrePaquete;
            $data['PrecioUnitarioDescarga'] = $paquete->PrecioUnitarioDescarga;
            $data['Bonificacion'] = (int)$paquete->Bonificacion;
            $data['PrecioUnitarioBonificacion'] = $paquete->PrecioUnitarioBonificacion;
            $data['CostoDia'] = $paquete->CostoDia;
            $data['TipoPaquete'] = $paquete->BNF_TipoPaquete_id;
            $estado = true;
        } catch (\Exception $ex) {
            $estado = false;
            $data = array();
        }

        if ($estado == true) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => $estado,
                        'results' => $data,
                        'message' => 'Detalles del Paquete'
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => $estado,
                        'results' => $data,
                        'message' => 'Error al recuperar la información'
                    )
                )
            );
        }
        return $response;
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $paquete = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
        $factura = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
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
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                null,
                null,
                $paquete,
                $factura,
                $empresa_id
            );
        } elseif ($fechafin == null) {
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                $fechainicio,
                date("Y-m-d"),
                $paquete,
                $factura,
                $empresa_id
            );
        } elseif ($fechainicio == null) {
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                date("1990-01-01"),
                $fechafin,
                $paquete,
                $factura,
                $empresa_id
            );
        } else {
            $paquetes = $this->getPaqueteEmpresaProveedorTable()->getPaquetesComprados(
                $fechainicio,
                $fechafin,
                $paquete,
                $factura,
                $empresa_id
            );
        }
        $registros = count($paquetes);
        $objPHPExcel = new PHPExcel();

        if ($registros > 0) {
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Paquetes")
                ->setSubject("Paquetes")
                ->setDescription("Documento listando las Paquetes")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Paquetes");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:O' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:O' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Fecha de Compra')
                ->setCellValue('B1', 'Tipo Paquete')
                ->setCellValue('C1', 'Nombre del Paquete')
                ->setCellValue('D1', 'Asesor')
                ->setCellValue('E1', 'Precio')
                ->setCellValue('F1', 'Cantidad')
                ->setCellValue('G1', 'Factura')
                ->setCellValue('H1', 'Costo Por Lead')
                ->setCellValue('I1', 'Máximo de Leads')
                ->setCellValue('J1', 'Cantidad de Descargas')
                ->setCellValue('K1', 'Precio Unitario por Descarga')
                ->setCellValue('L1', 'Bonificación')
                ->setCellValue('M1', 'Precio Unitario por Bonificación')
                ->setCellValue('N1', 'Numero de Días')
                ->setCellValue('O1', 'Costo por Día');

            $i = 2;

            foreach ($paquetes as $registro) {
                if ($registro->TipoPaquete == 'Descarga') {
                    $registro->Cantidad = (($registro->Cantidad) != 1)
                        ? ((int)$registro->Cantidad) . ' Descargas' : '1 Descarga';
                } elseif ($registro->TipoPaquete == 'Presencia') {
                    $registro->Cantidad = (($registro->Cantidad) != 1)
                        ? ((int)$registro->Cantidad) . ' Días' : '1 Día';
                } elseif ($registro->TipoPaquete == 'Lead') {
                    $registro->Cantidad = (($registro->Cantidad) != 1)
                        ? ((int)$registro->Cantidad) . ' Leads' : '1 Lead';
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->FechaCompra)
                    ->setCellValue('B' . $i, $registro->TipoPaquete)
                    ->setCellValue('C' . $i, $registro->NombrePaquete)
                    ->setCellValue('D' . $i, $registro->Nombres . ' ' . $registro->Apellidos)
                    ->setCellValue('E' . $i, $registro->Precio)
                    ->setCellValue('F' . $i, $registro->Cantidad)
                    ->setCellValue('G' . $i, $registro->Factura)
                    ->setCellValue('H' . $i, $registro->CostoPorLead)
                    ->setCellValue('I' . $i, $registro->MaximoLeads)
                    ->setCellValue('J' . $i, $registro->CantidadDescargas)
                    ->setCellValue('K' . $i, $registro->PrecioUnitarioDescarga)
                    ->setCellValue('L' . $i, $registro->Bonificacion)
                    ->setCellValue('M' . $i, $registro->PrecioUnitarioBonificacion)
                    ->setCellValue('N' . $i, $registro->NumeroDias)
                    ->setCellValue('O' . $i, $registro->CostoDia);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="PaquetesComprados.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
