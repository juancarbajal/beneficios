<?php

namespace Premios\Controller;

use Auth\Service\Csrf;
use Auth\Form\BaseForm;
use Premios\Form\FormReporteProveedorOfertas;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\NotEmpty;
use Zend\Validator\Db\RecordExists;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class ProveedorOfertasController extends AbstractActionController
{

    #region ObjectTables
    public function getOfertaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
    }

    public function getCampaniaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getCuponPremiosTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosTable');
    }

    public function getCuponPremiosLogTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponPremiosLogTable');
    }

    public function getOfertaPremiosAtributosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosAtributosTable');
    }

    #endregion

    public function inicializacionBusqueda()
    {
        $dataEmpCli = array();
        $filterEmpCli = array();

        try {
            foreach ($this->getCampaniaPremiosTable()->getEmpresasProveedora() as $empresa) {
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
        $oferta = null;
        $atributo = null;
        $split = false;
        $data = null;
        $errors = array('empresa' => null, 'campania' => null, 'ofertas' => null);
        $request = $this->getRequest();
        $tipo = $this->identity()->TipoUsuario;
        if ($request->isPost()) {
            $data = $request->getPost();

            #region Valid Empresa
            $valid = new NotEmpty();
            $valid->isValid($data['empresa']);
            if ($valid->getMessages())
                $errors['empresa'] = 'El valor no existe';
            $valid = new RecordExists(
                array(
                    'table' => 'BNF_Empresa',
                    'field' => 'id',
                    'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
                )
            );
            $valid->isValid($data['empresa']);
            if ($valid->getMessages())
                $errors['empresa'] = 'Se requiere valor y no puede estar vacío';
            #endregion

            #region Valid Campaña
            $valid = new NotEmpty();
            $valid->isValid($data['campania']);
            if ($valid->getMessages())
                $errors['campania'] = 'El valor no existe';
            $valid = new RecordExists(
                array(
                    'table' => 'BNF3_Campanias',
                    'field' => 'id',
                    'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
                )
            );
            $valid->isValid($data['campania']);
            if ($valid->getMessages())
                $errors['campania'] = 'Se requiere valor y no puede estar vacío';
            #endregion

            #region Valid Oferta
            $valid = new NotEmpty();
            $data['ofertas'] = explode('-', $data['ofertas']);
            if($data['ofertas'][1] == 'A') {
                $split = true;
            }
            $data['ofertas'] = $data['ofertas'][0];

            $valid->isValid($data['ofertas']);
            if ($valid->getMessages())
                $errors['ofertas'] = 'Se requiere valor y no puede estar vacío';
            $valid = new RecordExists(
                array(
                    'table' => 'BNF3_Oferta_Premios',
                    'field' => 'id',
                    'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
                )
            );
            $valid->isValid($data['ofertas']);
            if ($valid->getMessages()) {
                $valid = new RecordExists(
                    array(
                        'table' => 'BNF3_Oferta_Premios_Atributos',
                        'field' => 'id',
                        'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
                    )
                );
                $valid->isValid($data['ofertas']);
                if ($valid->getMessages()) {
                    $errors['ofertas'] = 'El valor no existe';
                } else {
                    $split = true;
                }
            }
            #endregion

            if (!$errors['empresa'] && !$errors['campania'] && !$errors['ofertas']) {

                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()
                    ->setCreator("Beneficios.pe")
                    ->setLastModifiedBy("Beneficios.pe")
                    ->setTitle("Reporte de Proveedor Ofertas Premios")
                    ->setSubject("Reporte Proveedor Ofertas Premios")
                    ->setDescription("Documento Reporte de Proveedor Ofertas Premios")
                    ->setKeywords("Beneficios.pe")
                    ->setCategory("Reporte Proveedor Ofertas Premios");

                #region Style
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

                if($split) {
                    $resultado = $this->getCuponPremiosTable()->getCuponesByAtributo((int)$data['ofertas']);
                    $atributo = $this->getOfertaPremiosAtributosTable()->getOfertaPremiosAtributos((int)$data['ofertas']);
                    $oferta = $atributo->BNF3_Oferta_Premios_id;
                } else {
                    $resultado = $this->getCuponPremiosTable()->getCuponesForOferta((int)$data['ofertas']);
                    $oferta = $data['ofertas'];
                }

                $registros = count($resultado);
                $inicio = 7;

                #region General
                $empresa = $this->getEmpresaTable()->getEmpresa((int)$data['empresa']);
                $campania = $this->getCampaniaPremiosTable()->getCampaniasP((int)$data['campania']);
                $oferta = $this->getOfertaPremiosTable()->getOfertaPremios((int)$oferta);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);
                $objPHPExcel->getActiveSheet()->getStyle('C7:C' . ($inicio + $registros))->applyFromArray($cellArray);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Reporte proveedor ofertas Premios')
                    ->mergeCells('A1:B1')
                    ->setCellValue('A3', 'Empresa Proveedor: ')
                    ->setCellValue('A4', 'Campañas: ')
                    ->setCellValue('A5', 'Oferta: ');

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B3', (!empty($empresa->NombreComercial) ? $empresa->NombreComercial : ''))
                    ->setCellValue('B4', (!empty($campania->NombreCampania) ? $campania->NombreCampania : ''))
                    ->setCellValue('B5', (!empty($oferta->Titulo) ? $oferta->Titulo .
                        (($split) ? ' - ' . $atributo->NombreAtributo : '') : ''));

                $objPHPExcel->getActiveSheet()->setAutoFilter('A7:D' . ($registros + $inicio));
                $objPHPExcel->getActiveSheet()->getStyle('A8:D' . ($inicio + $registros))->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->applyFromArray($styleArray);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A7', 'Cupón')
                    ->setCellValue('B7', 'Ingreso (PB)')
                    ->setCellValue('C7', 'Estado')
                    ->setCellValue('D7', 'Fecha ult act');
                #endregion

                #region Data
                if ($registros > 0) {
                    $i = $inicio + 1;

                    foreach ($resultado as $registro) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $registro->CodigoCupon)
                            ->setCellValue('B' . $i, $registro->PrecioBeneficio)
                            ->setCellValue('C' . $i, $registro->EstadoCupon);
                        switch ($registro->EstadoCupon) {
                            case 'Eliminado':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaEliminado);
                                break;
                            case 'Generado':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaGenerado);
                                break;
                            case 'Redimido':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaRedimido);
                                break;
                            case 'Por Pagar':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaPorPagar);
                                break;
                            case 'Pagado':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaPagado);
                                break;
                            case 'Stand By':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaStandBy);
                                break;
                            case 'Anulado':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaAnulado);
                                break;
                            case 'Finalizado':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaFinalizado);
                                break;
                            case 'Caducado':
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('D' . $i, $registro->FechaCaducado);
                                break;
                        }
                        $i++;
                    }

                    $i++;
                    foreach ($resultado as $registro) {
                        $i++;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, 'Historial del cupón');
                        $i++;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, 'Empresa proveedora:')
                            ->setCellValue(
                                'B' . $i,
                                (!empty($empresa->NombreComercial) ? $empresa->NombreComercial : '')
                            );
                        $i++;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, 'Campaña:')
                            ->setCellValue(
                                'B' . $i,
                                (!empty($campania->NombreCampania) ? $campania->NombreCampania : '')
                            );
                        $i++;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, 'Oferta:')
                            ->setCellValue('B' . $i, (!empty($oferta->Titulo) ? $oferta->Titulo .
                                (($split) ? ' - ' . $atributo->NombreAtributo : '') : ''));
                        $i++;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, 'Cupón:')
                            ->setCellValue('B' . $i, $registro->CodigoCupon);
                        $i++;
                        $i++;
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':C' . $i)->applyFromArray($styleArray);
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, 'Estado')
                            ->setCellValue('B' . $i, 'Fecha ult act')
                            ->setCellValue('C' . $i, 'Ingreso');

                        $logs = $this->getCuponPremiosLogTable()->getCuponPremiosLogByCupon($registro->id);

                        foreach ($logs as $value) {
                            $i++;
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':C' . $i)->applyFromArray($styleArray2);
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, $value->EstadoCupon)
                                ->setCellValue('B' . $i, $value->FechaCreacion)
                                ->setCellValue('C' . $i, $registro->PrecioBeneficio);
                        }
                        $i++;
                    }
                }
                #endregion

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="ReporteProveedorOfertasPremios.xlsx"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
                exit;
            }
        }

        $dataForm = $this->inicializacionBusqueda();
        if (!$this->identity()) {
            $this->redirect()->toUrl('/login');
        }

        if ($this->identity()->TipoUsuario == "proveedor") {
            $data['empresa'] = $this->identity()->BNF_Empresa_id;
            $type = "proveedor";
            $nombre = $datosEmpresa = $this->getEmpresaTable()->getEmpresa($data['empresa'])->NombreComercial;
        } else {
            $type = "admin";
            $nombre = "";
        }
        $form = new FormReporteProveedorOfertas('reporte', $dataForm[0], $type);

        if ($data)
            $form->setData($data);
        return new ViewModel(
            array(
                'form' => $form,
                'type' => $type,
                'nombre' => $nombre,
                'reportespremios' => 'active',
                'reportesproofertas' => 'active',
                'errors' => $errors
            )
        );
    }

    public function getDataEmpresaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $response = $this->getResponse();
        $request = $this->getRequest();
        $dataCampanias = array();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    if ($result = $this->getEmpresaTable()->getEmpresa($id)) {
                        $campanias = $this->getCampaniaPremiosTable()->getCampaniasPByEmpresaProv($id);
                        foreach ($campanias as $value) {
                            $dataCampanias[] = array('id' => $value->id, 'text' => $value->NombreCampania);
                        }

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
                'campanias' => $dataCampanias,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function getDataOfertasAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $response = $this->getResponse();
        $request = $this->getRequest();
        $dataOfertas = array();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            $empresa = $post_data['empresa_id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    if ($result = $this->getCampaniaPremiosTable()->getIfExist($id)) {
                        $ofertas = $this->getOfertaPremiosTable()
                            ->getAllOfertaPremiosByCampaniaAndEmpresaProv($id, $empresa);
                        foreach ($ofertas as $value) {
                            $dataOfertas[] = array(
                                'id' => $value->id . (($value->Atributo == null) ? '-O' : '-A'),
                                'text' => ($value->Atributo == null)
                                    ? $value->Titulo : $value->Titulo . ' - ' . $value->Atributo);
                        }

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
                'ofertas' => $dataOfertas,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }
}

