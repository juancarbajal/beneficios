<?php

namespace Reportes\Controller;

use DateTime;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Reportes\Form\PeriodoForm;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ReporteController extends AbstractActionController
{
    private $cliente;
    private $oferta;
    private $empresa;
    private $paquetes;
    private $envios;
    const max_results = 10000;

    function check_in_range($start_date, $end_date, $fromUser)
    {
        if (new DateTime($fromUser) >= new DateTime($start_date)
            && new DateTime($fromUser) <= new DateTime($end_date)
        ) {
            return true;
        }
        return false;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function reporteUnoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $form = new PeriodoForm();
        return new ViewModel(
            array(
                "reportes" => 'active',
                "reporteuno" => 'active',
                "form" => $form
            )
        );
    }

    public function exportReporteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $fini = null;
        $ffin = null;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $fini = (!empty($request->getPost()->FechaInicio)) ? $request->getPost()->FechaInicio : null;
            $ffin = (!empty($request->getPost()->FechaFin)) ? $request->getPost()->FechaFin : null;
        }

        $this->cliente = $this->serviceLocator->get('Cliente\Model\ClienteTable');
        $totalDni = $this->cliente->getTotalDniRegistrados($fini, $ffin);
        $totalCorreo = $this->cliente->getTotalCorreosRegistrados($fini, $ffin);

        $this->oferta = $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
        $totalCaducadas = $this->oferta->getTotalOfertasCaducadas($fini, $ffin);
        $totalPublicadas = $this->oferta->getTotalOfertasPublicadas($fini, $ffin);
        $totalPendientes = $this->oferta->getTotalOfertasPendientes($fini, $ffin);

        $this->empresa = $this->serviceLocator->get('Empresa\Model\EmpresaTable');
        $totalClientes = $this->empresa->getTotalClientes($fini, $ffin);
        $totalProveedoras = $this->empresa->getTotalProveedoras($fini, $ffin);

        $this->paquetes = $this->serviceLocator->get('Paquete\Model\PaqueteEmpresaProveedorTable');
        $totalpaquetes = $this->paquetes->getTotalPaquetesComprados($fini, $ffin);
        $compras = $this->paquetes->getDetallePaquetesCompradosLead($fini, $ffin);

        $this->envios = $this->serviceLocator->get('Reportes\Model\Table\OfertaFormClienteTable');
        $totalenvios = $this->envios->getTotalEnviosPorEmpresa($fini, $ffin);
        $dataenvios = array();
        foreach ($totalenvios as $envio) {
            $dataenvios[$envio->Empresa] = $envio->Total;
        }

        $registros = count($compras) + 11;
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Uno")
                ->setSubject("Reporte Uno")
                ->setDescription("Documento")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reportes");

            $objPHPExcel->getActiveSheet()->setAutoFilter('B11:F' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

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

            $styleArray3 = array(
                'font' => array(
                    'bold' => true,
                    'underline' => true
                ),
            );

            $objPHPExcel->getActiveSheet()->getStyle('B11:F' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B11:F11')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray3);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . 1, "REPORTE 1");

            $i = 2;
            if ($fini != null || $ffin != null) {
                if ($fini == null) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, "Periodo")
                        ->setCellValue('C' . $i, 'al')
                        ->setCellValue('D' . $i, $ffin);
                } elseif ($ffin == null) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, "Periodo del ")
                        ->setCellValue('C' . $i, $fini);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, "Periodo del ")
                        ->setCellValue('C' . $i, $fini)
                        ->setCellValue('D' . $i, 'al')
                        ->setCellValue('E' . $i, $ffin);
                }
            }

            $i = $i + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, "Dnis Generados")
                ->setCellValue('C' . $i, $totalDni);

            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, "Emails Obtenidos")
                ->setCellValue('C' . $i, $totalCorreo)
                ->setCellValue('E' . $i, "Ofertas Caducadas")
                ->setCellValue('F' . $i, $totalCaducadas);

            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, "Empresa Cliente registradas")
                ->setCellValue('C' . $i, $totalClientes)
                ->setCellValue('E' . $i, "Ofertas en borrador")
                ->setCellValue('F' . $i, $totalPendientes);

            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, "Empresa Proveedora Registrada")
                ->setCellValue('C' . $i, $totalProveedoras)
                ->setCellValue('E' . $i, "Ofertas Publicadas")
                ->setCellValue('F' . $i, $totalPublicadas);

            $i++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, "Paquetes vendidos")
                ->setCellValue('C' . $i, $totalpaquetes);

            $i = $i + 3;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, "Empresa")
                ->setCellValue('C' . $i, "Nombre Paquete")
                ->setCellValue('D' . $i, "Costo por Lead")
                ->setCellValue('E' . $i, "Leads Generados")
                ->setCellValue('F' . $i, "Monto a Facturar");

            $i++;
            foreach ($compras as $cd) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B' . $i, $cd->NombreComercial)
                    ->setCellValue('C' . $i, $cd->NombrePaquete)
                    ->setCellValue('D' . $i, $cd->CostoPorLead)
                    ->setCellValue('E' . $i, (int)$dataenvios[$cd->BNF_Empresa_id])
                    ->setCellValue('F' . $i, $cd->CostoPorLead * (int)$dataenvios[$cd->BNF_Empresa_id]);
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_uno.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    public function reporteTresAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresas = array();
        $getEmpresaTable = $this->serviceLocator->get('Empresa\Model\EmpresaTable');
        $dataEmpresas = $getEmpresaTable->getEmpresaCli();
        foreach ($dataEmpresas as $e) {
            $empresas[$e->id] = $e->NombreComercial . " (" . $e->RazonSocial . ") - " . $e->Ruc;
        }
        $form = new PeriodoForm('periodo', $empresas);

        return new ViewModel(
            array(
                "reportes" => 'active',
                "reportetres" => 'active',
                "form" => $form,
            )
        );
    }

    public function exportTresAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultados = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $fechaInicio_defecto = '2015-01-01';
            $fechaFin_defecto = date('Y-m-d');
            $fechaInicio = ($request->getPost()->FechaInicio2 == '') ? $fechaInicio_defecto : $request->getPost()->FechaInicio2;
            $fechaFin = ($request->getPost()->FechaFin2 == '') ?
                $fechaFin_defecto : $request->getPost()->FechaFin2;
            $id_empresa = ($request->getPost()->empresa == '') ? '' : $request->getPost()->empresa;
            $emails = ($request->getPost()->Emails == '') ? '' : $request->getPost()->Emails;


            $data = array(
                'emails' => $emails,
                'id_empresa' => $id_empresa,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            );

            $config = $this->getServiceLocator()->get('Config');
            $ch = curl_init($config['API_LARAVEL_HOST'] . "api/v1/reporte_crm");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_USERPWD, $config['API_LARAVEL_USER'] . ":" . $config['API_LARAVEL_PASS']);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            if (!$response->error) {
                $this->flashMessenger()->addSuccessMessage('El reporte se enviará a su correo en breves minutos');
            } else {
                $this->flashMessenger()->addErrorMessage('Error en la generacion del reporte');
            }
        }
        return $this->redirect()->toRoute('reportes', array('action' => 'reporteTres'));
    }

    public function reporteDnisAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $getEmpresaTable = $this->serviceLocator->get('Empresa\Model\EmpresaTable');

        $empresas = array();
        $dataEmpresas = $getEmpresaTable->getEmpresaCli();
        foreach ($dataEmpresas as $e) {
            $empresas[$e->id] = $e->NombreComercial . " (" . $e->RazonSocial . ") - " . $e->Ruc;
        }

        $busqueda = array(
            'Empresa' => 'NombreComercial',
            'Cantidad' => 'CantidadClientes'
        );


        $empresa = null;
        $order_by_o = null;

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : null;
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

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        $request = $this->getRequest();

        $form = new PeriodoForm('periodo', $empresas);
        $form->get('submit')->setAttribute('value', 'Buscar');
        if ($request->isPost()) {
            $empresa = (!empty($request->getPost()->empresa)) ? $request->getPost()->empresa : null;
            $form->setData(array("empresa" => $empresa));
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $form->setData(array("empresa" => (int)$empresa));
        }

        $data = $getEmpresaTable->getCantClientesEmpresa((int)$empresa, $order_by, $order);

        $id_empresa = (int)$empresa;
        if (count($data) >= 1 && $empresa != null) {
            $empresa = (string)$empresa;
        } else {
            $empresa = null;
        }
        $paginator = new Paginator(new paginatorIterator($data, $order_by));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(
            array(
                'form' => $form,
                'datos' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'q1' => $empresa,
                'p' => $page,
                'id_empresa' => $id_empresa,
                "reportes" => 'active',
                "reportednis" => 'active'
            )
        );
    }

    public function exportDniAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $getEmpresaTable = $this->serviceLocator->get('Empresa\Model\EmpresaTable');

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

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = (!empty($request->getPost()->id_empresa)) ? $request->getPost()->id_empresa : null;

            $empresas = $getEmpresaTable->getCantClientesEmpresa((int)$empresa);

            $objPHPExcel = new PHPExcel();
            if (count($empresas) > 0) {
                //Informacion del excel
                $objPHPExcel->
                getProperties()
                    ->setCreator("Beneficios.pe")
                    ->setLastModifiedBy("Beneficios.pe")
                    ->setSubject("Reporte Dni")
                    ->setDescription("Reporte Dni")
                    ->setKeywords("Beneficios.pe")
                    ->setCategory("Reporte Dni");
                $objPHPExcel->getActiveSheet()->setTitle("Reporte Dni");

                //desactiva cuadricula
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);

                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

                $objPHPExcel->getActiveSheet()->setAutoFilter('B3:C3');
                $objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('B4:C' . (count($empresas) + 3))->applyFromArray($styleArray2);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Reporte de la Cantidad de DNIs Activos por Empresa')
                    ->setCellValue('B3', 'Empresa')
                    ->setCellValue('C3', 'Cantidad de Dnis');

                $fila = 4;
                foreach ($empresas as $data) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow(1, $fila, $data->NombreComercial)
                        ->setCellValueByColumnAndRow(
                            2,
                            $fila,
                            ($data->CantidadClientes != 0) ? $data->CantidadClientes : '0'
                        );
                    $fila++;
                }

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="ReporteDnis.xlsx"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }
        }
        exit;
    }

    public function exportCuatroAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultados = null;
        $request = $this->getRequest();
        $getMetCliente = $this->serviceLocator->get('Reportes\Model\Table\DmMetClienteTable');
        $getRubros = $this->serviceLocator->get('Rubro\Model\Table\RubroTable');
        $getEmpresa = $this->serviceLocator->get('Empresa\Model\EmpresaTable');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);

        if ($request->isPost()) {
            $fechaInicio_defecto = '2015-01-01';
            $fechaFin_defecto = date('Y-m-d');
            $fechaInicio = ($request->getPost()->FechaInicio2 == '') ? $fechaInicio_defecto : $request->getPost()->FechaInicio2;
            $fechaFin = ($request->getPost()->FechaFin2 == '') ?
                $fechaFin_defecto : $request->getPost()->FechaFin2;
            $id_empresa = ($request->getPost()->empresa == '') ? '' : $request->getPost()->empresa;

            $nombre_empresa = ($id_empresa == '')
                ? 'Todo el site'
                : $getEmpresa->getEmpresa($id_empresa)->NombreComercial;

            $lista_rubros = $getRubros->fetchAll();
            $count_rubros = count($lista_rubros);

            $getData = $getMetCliente->getDataRubros($lista_rubros, $id_empresa, $fechaInicio, $fechaFin);

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

            $objPHPExcel = new PHPExcel();
            if ($getData > 0) {
                //Informacion del excel
                $objPHPExcel->getProperties()
                    ->setCreator("Beneficios.pe")
                    ->setLastModifiedBy("Beneficios.pe")
                    ->setTitle("Reporte 4")
                    ->setSubject("Reporte 4")
                    ->setDescription("Reporte 4")
                    ->setKeywords("Beneficios.pe")
                    ->setCategory("Reporte 4");

                //desactiva cuadricula
                $objPHPExcel->getActiveSheet()->setShowGridlines(false);

                $letra = 65;
                $letra_tope = 90;
                $letra_inicio = 64;
                $letra_final = 'L';
                $letra_chr = null;
                while ($letra < (65 + $count_rubros + 11)) {
                    if ($letra <= $letra_tope) {
                        if ($letra_tope > 90) {
                            $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                            $letra_chr = chr($letra_inicio) . chr($letra_chr);
                        } else {
                            $letra_chr = chr($letra);
                        }
                    } elseif ($letra <= $letra_tope + 26) {
                        $letra_inicio++;
                        $letra_tope = $letra_tope + 26;
                        $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                        $letra_chr = chr($letra_inicio) . chr($letra_chr);
                    }

                    $objPHPExcel->getActiveSheet()->getColumnDimension($letra_chr)->setAutoSize(true);
                    $letra++;
                    $letra_final = $letra_chr;
                }

                $objPHPExcel->getActiveSheet()->getStyle('A1:' . $letra_final . '' . (count($getData) + 1))->applyFromArray($styleArray2);

                $objPHPExcel->getActiveSheet()->getStyle('A1:' . $letra_final . '1')->applyFromArray($styleArray);

                $objPHPExcel->getActiveSheet()->getStyle('A1:' . $letra_final . '1')->applyFromArray($styleArray2);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(0, 1, 'Empresa')
                    ->setCellValueByColumnAndRow(1, 1, 'Correo');
                $column = 2;
                foreach ($getRubros->fetchAll() as $data) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($column, 1, $data->Nombre);
                    $column++;
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($column, 1, 'Fecha última descarga')
                    ->setCellValueByColumnAndRow($column + 1, 1, 'Nombre')
                    ->setCellValueByColumnAndRow($column + 2, 1, 'Apellidos')
                    ->setCellValueByColumnAndRow($column + 3, 1, 'Edad')
                    ->setCellValueByColumnAndRow($column + 4, 1, 'Estado Civil')
                    ->setCellValueByColumnAndRow($column + 5, 1, 'Genero')
                    ->setCellValueByColumnAndRow($column + 6, 1, 'Hijos')
                    ->setCellValueByColumnAndRow($column + 7, 1, 'Distrito Vive')
                    ->setCellValueByColumnAndRow($column + 8, 1, 'Distrito Trabaja');
                $row = 2;
                foreach ($getData as $data) {
                    $data = (array)$data;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow(0, $row, $nombre_empresa)
                        ->setCellValueByColumnAndRow(1, $row, $data['Correo']);
                    $column = 2;
                    foreach ($getRubros->fetchAll() as $datar) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($column, $row, (int)$data['Rubro' . $datar->id]);
                        $column++;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($column, $row, $data['FechaGenerado'])
                        ->setCellValueByColumnAndRow($column + 1, $row, $data['Nombre'])
                        ->setCellValueByColumnAndRow($column + 2, $row, $data['Apellidos'])
                        ->setCellValueByColumnAndRow($column + 3, $row, $data['Edad'])
                        ->setCellValueByColumnAndRow($column + 4, $row, $data['estado'])
                        ->setCellValueByColumnAndRow($column + 5, $row, $data['Genero'])
                        ->setCellValueByColumnAndRow($column + 6, $row, $data['hijos'])
                        ->setCellValueByColumnAndRow($column + 7, $row, $data['distrito_vive'])
                        ->setCellValueByColumnAndRow($column + 8, $row, $data['distrito_trabaja']);
                    $row++;
                }
            }
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Reporte4.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
    }
}

