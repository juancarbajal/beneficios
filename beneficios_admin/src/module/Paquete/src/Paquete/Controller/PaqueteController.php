<?php

namespace Paquete\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Paquete\Model\BolsaTotal;
use Paquete\Model\Data\BuscarPaqueteData;
use Paquete\Model\Filter\BuscarPaqueteFilter;
use Paquete\Form\BuscarPaqueteForm;
use Paquete\Form\PaqueteForm;
use Paquete\Model\Paquete;
use Paquete\Model\PaquetePais;
use Paquete\Model\PaqueteEmpresaProveedor;
use Paquete\Form\AsignarForm;
use Paquete\Form\EditarAsignacionForm;
use Zend\Json\Json;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Paginator\Paginator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class PaqueteController extends AbstractActionController
{
    const TIPO_PAQUETE_DESCARGA = 1;
    const TIPO_PAQUETE_PRESENCIA = 2;
    const TIPO_PAQUETE_LEAD = 3;
    const ESTADO_PAQUETE_ELIMINADO = 1;

    #region ObjectTables
    public function getBolsaTable()
    {
        return $this->serviceLocator->get('Paquete\Model\Table\BolsaTotalTable');
    }

    public function getPaisTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaisTable');
    }

    public function getPaqueteProvTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaqueteEmpresaProveedorTable');
    }

    public function getPaquetePaisTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaquetePaisTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getPaqueteTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaqueteTable');
    }

    public function getTipoPaqueteTable()
    {
        return $this->serviceLocator->get('Paquete\Model\TipoPaqueteTable');
    }

    public function getUsuarioTable()
    {
        return $this->serviceLocator->get('Usuario\Model\Table\UsuarioTable');
    }

    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }
    #endregion

    #region Inicializacion
    public function extraerPais()
    {
        $cbxPais = array();
        try {
            $datosp = $this->getPaisTable()->fetchAll();
            foreach ($datosp as $dato) {
                $cbxPais[$dato->id] = $dato->NombrePais;
            }
        } catch (\Exception $ex) {
            return $cbxPais;
        }
        return $cbxPais;
    }

    public function extraerEmpresa()
    {
        $cbxEmpresa = array();
        try {
            $datose = $this->getEmpresaTable()->fetchAll();
            foreach ($datose as $dato) {
                $cbxEmpresa[$dato->id] = $dato->NombreComercial;
            }
        } catch (\Exception $ex) {
            return $cbxEmpresa;
        }
        return $cbxEmpresa;
    }

    public function extraerEmpresaProv()
    {
        $cbxEmpresa = array();
        try {
            $datose = $this->getEmpresaTable()->getEmpresaProv();
            foreach ($datose as $dato) {
                $cbxEmpresa[$dato->id] = $dato->NombreComercial . ' - ' .
                    $dato->RazonSocial . ' - ' . $dato->Ruc;
            }
        } catch (\Exception $ex) {
            return $cbxEmpresa;
        }
        return $cbxEmpresa;
    }

    public function extraerPaquete()
    {
        $cbxTipo = array();
        try {
            $datos = $this->getPaqueteTable()->fetchAll();
            foreach ($datos as $dato) {
                $cbxTipo[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $cbxTipo;
        }
        return $cbxTipo;
    }

    public function extraerTipoPaquete()
    {
        $cbxTipo = array();
        try {
            $datos = $this->getTipoPaqueteTable()->fetchAll();
            foreach ($datos as $dato) {
                $cbxTipo[$dato->id] = $dato->NombreTipoPaquete;
            }
        } catch (\Exception $ex) {
            return $cbxTipo;
        }
        return $cbxTipo;
    }

    public function extraerUsuarioAsesor()
    {
        $cbxasesor = array();
        try {
            $datos = $this->getUsuarioTable()->getUsuarioAsesor();
            foreach ($datos as $dato) {
                $cbxasesor[$dato->id] = $dato->Nombres . ' ' . $dato->Apellidos;
            }
        } catch (\Exception $ex) {
            return $cbxasesor;
        }
        return $cbxasesor;
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $busqueda = array(
            'Paquete' => 'NombrePaquete',
            'TipoPaquete' => 'TipoPaquete',
            'Bolsa' => 'BolsaActual',
            'Empresa' => 'NombreComercial',
            'Activo' => 'Eliminado',
            'Precio' => 'Precio',
            'Cantidad' => 'Cantidad'
        );

        $datos = new BuscarPaqueteData($this);
        $form = new BuscarPaqueteForm('paquete', $datos->getFormData());
        $paquetes = null;
        $paquetese = null;
        $pais = 0;
        $tipo = 0;
        $nombre = 0;
        $inicio = null;
        $fin = null;

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];

        } else {
            $order_by_o = 'id';
            $order_by = 'FechaCreacion';
        }

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $this->getRequest()->getPost()->toArray();
            $validate = new BuscarPaqueteFilter();
            $form->setInputFilter(
                $validate->getInputFilter($datos->getFilterData(), $post)
            );

            $form->setData($post);

            if ($form->isValid()) {
                $pais = (!empty($request->getPost()->NombrePais))
                    ? $request->getPost()->NombrePais : 0;
                $tipo = (!empty($request->getPost()->TipoPaquete))
                    ? $request->getPost()->TipoPaquete : 0;
                $nombre = (!empty($request->getPost()->RazonSocial))
                    ? $request->getPost()->RazonSocial : 0;
                $inicio = (!empty($request->getPost()->FechaInicio))
                    ? $request->getPost()->FechaInicio : null;
                $fin = (!empty($request->getPost()->FechaFin))
                    ? $request->getPost()->FechaFin : null;
            }

        } else {
            $pais = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : 0;
            $tipo = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : 0;
            $nombre = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : 0;
            $inicio = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;
            $fin = $this->params()->fromRoute('q5') ? $this->params()->fromRoute('q5') : null;

            $validator = new Date(array('format' => 'Y-m-d'));

            if ($inicio) {
                if (!$validator->isValid($inicio)) {
                    $this->getResponse()->setStatusCode(404);
                    return;
                }
            }

            if ($fin) {
                if (!$validator->isValid($fin)) {
                    $this->getResponse()->setStatusCode(404);
                    return;
                }
            }

            $form->setData(
                array(
                    "NombrePais" => $pais,
                    "RazonSocial" => $nombre,
                    "TipoPaquete" => $tipo,
                    "FechaInicio" => $inicio,
                    "FechaFin" => $fin
                )
            );
        }

        $paquetes = $this->getPaqueteProvTable()
            ->getDetailPaquete($pais, $tipo, $nombre, $inicio, $fin, $order_by, $order);

        $paginator = new Paginator(new paginatorIterator($paquetes, $order_by));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paquete' => 'active',
                'palistar' => 'active',
                'form' => $form,
                'paquetes' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'q1' => $pais,
                'q2' => $tipo,
                'q3' => $nombre,
                'q4' => $inicio ? $inicio : 0,
                'q5' => $fin ? $fin : 0,
                'p' => $page,
            )
        );
    }

    public function addAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $msg = null;
        $alert = 'danger';
        $tipopaquete = null;

        $form = new PaqueteForm($this->extraerPais(), $this->extraerTipoPaquete());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $paquete = new Paquete();

            ///////////valida segun el tipo de paquete
            $tipopaquete = $request->getPost()->BNF_TipoPaquete_id;
            if ($tipopaquete == $this::TIPO_PAQUETE_DESCARGA) {
                $form->setInputFilter($paquete->getInputFilter($this->extraerPais(), $this->extraerTipoPaquete()));
            } elseif ($tipopaquete == $this::TIPO_PAQUETE_PRESENCIA) {
                $form->setInputFilter($paquete->getInputFilterP($this->extraerPais(), $this->extraerTipoPaquete()));
            } elseif ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                $form->setInputFilter($paquete->getInputFilterL($this->extraerPais(), $this->extraerTipoPaquete()));
            } else {
                $form->setInputFilter($paquete->getInputFilter($this->extraerPais(), $this->extraerTipoPaquete()));
            }
            /////////

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $alert = 'success';
                $paquete->exchangeArray($form->getData());
                $id = $this->getPaqueteTable()->savePaquete($paquete, $tipopaquete);

                //relacionar paquete y pais
                $paquetepais = new PaquetePais();
                $paquetepais->BNF_Pais_id = (int)$request->getPost()->NombrePais;
                $paquetepais->BNF_Paquete_id = (int)$id;
                $this->getPaquetePaisTable()->savePaquetePais($paquetepais);
                /////

                //////crea mensage
                $msg[] = 'Paquete Registrado Correctamente';

                //////refreca el form
                $form = new PaqueteForm($this->extraerPais(), $this->extraerTipoPaquete());
                //////////
            }
        }
        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paquete' => 'active',
                'padd' => 'active',
                'msg' => $msg,
                'alert' => $alert,
                'form' => $form,
                'tipo' => $tipopaquete,
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $valid = null;
        $tipopaquete = '';

        ///carga datos cuando recibe un id
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('paquete', array('action' => 'add'));
        }
        try {
            $paquetes = $this->getPaqueteTable()->getDetailPaqueteEdit($id);
            foreach ($paquetes as $dato) {
                $paquete = $dato;
                $paquete->Eliminado = (int)$paquete->Eliminado;
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('paquete', array('action' => 'index'));
        }
        ////
        $form = new PaqueteForm($this->extraerPais(), $this->extraerTipoPaquete());

        $form->get('submit')->setAttribute('value', 'Editar');

        $form->bind($paquete);

        ////////obtiene valor del tipo de paquete enviado por post
        $tipopaquete = $paquete->BNF_TipoPaquete_id;

        $request = $this->getRequest();
        if ($request->isPost()) {
            ///////////valida segun el tipo de paquete
            $tipopaquete = $request->getPost()->BNF_TipoPaquete_id;
            if ($tipopaquete == $this::TIPO_PAQUETE_DESCARGA) {
                $form->setInputFilter($paquete->getInputFilter($this->extraerPais(), $this->extraerTipoPaquete()));
            } elseif ($tipopaquete == $this::TIPO_PAQUETE_PRESENCIA) {
                $form->setInputFilter($paquete->getInputFilterP($this->extraerPais(), $this->extraerTipoPaquete()));
            } elseif ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                $form->setInputFilter($paquete->getInputFilterL($this->extraerPais(), $this->extraerTipoPaquete()));
            } else {
                $form->setInputFilter($paquete->getInputFilter($this->extraerPais(), $this->extraerTipoPaquete()));
            }
            /////////

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getPaqueteTable()->savePaquete($paquete, $tipopaquete);

                ///relacionar paquete y pais
                $paquetepais = $this->getPaquetePaisTable()->getPaquetePaisP($id);
                $paquetepais->BNF_Pais_id = (int)$request->getPost()->NombrePais;
                $paquetepais->BNF_Paquete_id = (int)$id;
                $this->getPaquetePaisTable()->savePaquetePais($paquetepais);
                ///

                $this->flashMessenger()->addMessage('Paquete Modificado Correctamente');
                return $this->redirect()->toRoute('paquete', array('action' => 'list'));
            }
        }
        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paquete' => 'active',
                'padd' => 'active',
                'msg' => $valid,
                'tipo' => $tipopaquete,
                'id' => $id,
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $val = $this->getRequest()->getPost('val');
            $id = $this->getRequest()->getPost('id');
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== FALSE) AND
                    (filter_var($val, FILTER_VALIDATE_INT) !== FALSE) AND
                    $csrf->verifyToken($post_data['csrf'])
                ) {
                    $this->getPaqueteTable()->deletePaquete($id, $val);
                    $state = true;
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultado = $this->getPaqueteProvTable()->getPaqueteProvExport();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();

        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Paquetes")
                ->setSubject("Paquetes")
                ->setDescription("Documento listando las Paquetes")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Paquetes");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:U' . (int)$registros);
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);

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
            $objPHPExcel->getActiveSheet()->getStyle('A1:U' . ((int)$registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Nombre Comercial de la Empresa')
                ->setCellValue('C1', 'Nombre del Paquete')
                ->setCellValue('D1', 'Tipo de Paquete')
                ->setCellValue('E1', 'Asesor')
                ->setCellValue('F1', 'Fecha de Compra')
                ->setCellValue('G1', 'Costo Por Lead')
                ->setCellValue('H1', 'Máximo de Leads')
                ->setCellValue('I1', 'Factura')
                ->setCellValue('J1', 'Precio')
                ->setCellValue('K1', 'Cantidad de Descargas')
                ->setCellValue('L1', 'Precio Unitario por Descarga')
                ->setCellValue('M1', 'Bonificación')
                ->setCellValue('N1', 'Precio Unitario por Bonificación')
                ->setCellValue('O1', 'Numero de Días')
                ->setCellValue('P1', 'Costo por Día')
                ->setCellValue('Q1', 'Bolsa')
                ->setCellValue('R1', 'País')
                ->setCellValue('S1', 'Fecha de Creación')
                ->setCellValue('T1', 'Fecha de Actualización')
                ->setCellValue('U1', 'Eliminado');

            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->NombreComercial)
                    ->setCellValue('C' . $i, $registro->NombrePaquete)
                    ->setCellValue('D' . $i, $registro->TipoPaquete)
                    ->setCellValue('E' . $i, $registro->Nombres . ' ' . $registro->Apellidos)
                    ->setCellValue('F' . $i, $registro->FechaCompra)
                    ->setCellValue('G' . $i, $registro->CostoPorLead)
                    ->setCellValue('H' . $i, $registro->MaximoLeads)
                    ->setCellValue('I' . $i, $registro->Factura)
                    ->setCellValue('J' . $i, $registro->Precio)
                    ->setCellValue('K' . $i, $registro->CantidadDescargas)
                    ->setCellValue('L' . $i, $registro->PrecioUnitarioDescarga)
                    ->setCellValue('M' . $i, $registro->Bonificacion)
                    ->setCellValue('N' . $i, $registro->PrecioUnitarioBonificacion)
                    ->setCellValue('O' . $i, $registro->NumeroDias)
                    ->setCellValue('P' . $i, $registro->CostoDia)
                    ->setCellValue('Q' . $i, $registro->BolsaActual)
                    ->setCellValue('R' . $i, $registro->NombrePais)
                    ->setCellValue('S' . $i, $registro->FechaCreacion)
                    ->setCellValue('T' . $i, $registro->FechaActualizacion)
                    ->setCellValue('U' . $i, ((int)$registro->Eliminado == $this::ESTADO_PAQUETE_ELIMINADO)
                        ? 'Inactivo' : 'Activo');
                $i++;

            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Paquetes.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function assingAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $msg = null;
        $errors = null;
        $alert = 'danger';

        $form = new AsignarForm($this->extraerPais(), $this->extraerEmpresaProv(), $this->extraerUsuarioAsesor());

        $paquetes = $this->getPaqueteTable()->getPaquetes();
        $paquetes2 = $this->getPaqueteTable()->getPaquetes();
        $paquetespais = $this->getPaquetePaisTable()->fetchAll();
        $empresas = $this->getEmpresaTable()->getEmpresaProv();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $paqueteprov = new PaqueteEmpresaProveedor();

            ///////////valida segun el tipo de paquete
            $paquete = $this->getPaqueteTable()->getPaquete($request->getPost()->BNF_Paquete_id);
            //var_dump($paquete);exit;
            $tipopaquete = ($paquete) ? $paquete->BNF_TipoPaquete_id : 0;
            if ($tipopaquete == $this::TIPO_PAQUETE_DESCARGA || $tipopaquete == $this::TIPO_PAQUETE_PRESENCIA) {
                $form->setInputFilter(
                    $paqueteprov->getInputFilter(
                        $this->extraerEmpresaProv(),
                        $this->getPaqueteTable()->getPaquetes(),
                        $this->extraerUsuarioAsesor(),
                        $this->extraerPais()
                    )
                );
            } elseif ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                $form->setInputFilter(
                    $paqueteprov->getInputFilterL(
                        $this->extraerEmpresaProv(),
                        $this->getPaqueteTable()->getPaquetes(),
                        $this->extraerUsuarioAsesor(),
                        $this->extraerPais()
                    )
                );
            } else {
                $form->setInputFilter(
                    $paqueteprov->getInputFilter(
                        $this->extraerEmpresaProv(),
                        $this->getPaqueteTable()->getPaquetes(),
                        $this->extraerUsuarioAsesor(),
                        $this->extraerPais()
                    )
                );
            }
            /////////

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $alert = 'success';
                $paqueteprov->exchangeArray($form->getData());

                ///////////recupera datos de paquete segun el tipo de paquete
                $tipopaquete = $paquete->BNF_TipoPaquete_id;
                $paqueteprovasig = new PaqueteEmpresaProveedor();
                $paqueteprovasigs = $this->getPaqueteProvTable()
                    ->getPaqueteProvxTipo($request->getPost()->BNF_Empresa_id, $tipopaquete);
                foreach ($paqueteprovasigs as $dato) {
                    $paqueteprovasig->Bolsa += (int)$dato->Bolsa;
                }

                $paqueteprov->NombrePaquete = $this->getPaqueteTable()
                    ->getPaquete((int)$request->getPost()->BNF_Paquete_id)->Nombre;
                $paqueteprov->BNF_TipoPaquete_id = $tipopaquete;

                if ($tipopaquete == $this::TIPO_PAQUETE_DESCARGA) {
                    $paqueteprov->CantidadDescargas = $paquete->CantidadDescargas;
                    $paqueteprov->PrecioUnitarioDescarga = $paquete->PrecioUnitarioDescarga;
                    $paqueteprov->Bonificacion = $paquete->Bonificacion;
                    $paqueteprov->PrecioUnitarioBonificacion = $paquete->PrecioUnitarioBonificacion;
                    $paqueteprov->Bolsa = (int)$paqueteprov->CantidadDescargas + (int)$paqueteprov->Bonificacion;
                } elseif ($tipopaquete == $this::TIPO_PAQUETE_PRESENCIA) {
                    $paqueteprov->NumeroDias = $paquete->NumeroDias;
                    $paqueteprov->CostoDia = $paquete->CostoDia;
                    $paqueteprov->Bolsa = $paqueteprov->NumeroDias;
                } elseif ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                    $paqueteprov->Bolsa = (int)$request->getPost()->MaximoLeads;
                }
                $BolsaNueva = $paqueteprov->Bolsa;
                $paqueteprov->Bolsa += (int)$paqueteprovasig->Bolsa;
                $paqueteprov->Precio = $paquete->Precio;
                $paqueteprov->Factura = (int)$paqueteprov->Factura;
                /////////
                $this->getPaqueteProvTable()->savePaqueteProv($paqueteprov, 1, '');

                ////////relacionar con BolsaTotal
                $bolsa = $this->getBolsaTable()
                    ->getBolsaxEmprexTipo($request->getPost()->BNF_Empresa_id, $tipopaquete);
                $BolsaActual = (int)$bolsa->BolsaActual;

                $bolsatotal = new BolsaTotal();
                $bolsatotal->BNF_TipoPaquete_id = $tipopaquete;
                $bolsatotal->BNF_Empresa_id = $request->getPost()->BNF_Empresa_id;
                $bolsatotal->BolsaActual = $BolsaActual + $BolsaNueva;

                if ($bolsa->BolsaActual == 'NULL') {
                    $this->getBolsaTable()->saveBolsa($bolsatotal);
                } else {
                    $this->getBolsaTable()->editBolsa($bolsatotal);
                }

                //////crea mensage
                $msg[] = 'Paquete Asignado Correctamente';

                //////refreca el form
                $form = new AsignarForm(
                    $this->extraerPais(),
                    $this->extraerEmpresaProv(),
                    $this->extraerUsuarioAsesor()
                );
                //////////

                return new ViewModel(
                    array(
                        'beneficios' => 'active',
                        'passing' => 'active',
                        'paquete' => 'active',
                        'form' => $form,
                        'msg' => $msg,
                        'alert' => $alert,
                        'paquetes1' => $paquetes,
                        'paquetes2' => $paquetes2,
                        'empresas' => $empresas,
                        'paquetespais' => $paquetespais,
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paquete' => 'active',
                'passing' => 'active',
                'form' => $form,
                'msg' => $msg,
                'alert' => $alert,
                'paquetes1' => $paquetes,
                'paquetes2' => $paquetes2,
                'empresas' => $empresas,
                'paquetespais' => $paquetespais,
                'errors' => $errors
            )
        );
    }

    public function assingeditAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $msg = null;
        $errors = null;
        $alert = 'danger';
        $mesage = false;
        $id = (int)$this->params()->fromRoute('id', 0);
        $paqueteprov = new PaqueteEmpresaProveedor();
        if (!$id) {
            return $this->redirect()->toRoute('paquete', array('action' => 'assing'));
        }
        try {
            $paquetesprovs = $this->getPaqueteProvTable()->getPaqueteProvEdit($id);
            foreach ($paquetesprovs as $dato) {
                $paqueteprov = $dato;
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('paquete', array('action' => 'index'));
        }

        $form = new EditarAsignacionForm($this->extraerUsuarioAsesor());

        $paqueteprov->FechaCompra = date_format(date_create($paqueteprov->FechaCompra), 'Y-m-d');
        $paqueteprov->Factura = (int)$paqueteprov->Factura;

        $form->bind($paqueteprov);

        $request = $this->getRequest();
        if ($request->isPost()) {
            /////segun el tipo pauete se validad un inputfilter adecaduado
            $tipopaquete = $paqueteprov->BNF_TipoPaquete_id;
            if ($tipopaquete == $this::TIPO_PAQUETE_DESCARGA || $tipopaquete == $this::TIPO_PAQUETE_PRESENCIA) {
                $form->setInputFilter($paqueteprov->getInputFilterE($this->extraerUsuarioAsesor()));
            } elseif ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                $form->setInputFilter($paqueteprov->getInputFilterLE($this->extraerUsuarioAsesor()));
            }
            ////////
            $bolsaAt = (int)$paqueteprov->Bolsa;
            $BNF_Empresa_id = $paqueteprov->BNF_Empresa_id;

            $form->setData($request->getPost());

            if ($form->isValid()) {
                if ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                    $paqueteprov->Bolsa = (int)$request->getPost()->MaximoLeads;
                }
                $paqueteprov->Factura = (int)$paqueteprov->Factura;
                $this->getPaqueteProvTable()->savePaqueteProv($paqueteprov, 2, $tipopaquete);

                ////////editar BolsaTotal
                $bolsa = $this->getBolsaTable()
                    ->getBolsaxEmprexTipo($BNF_Empresa_id, $tipopaquete);
                $BolsaActual = (int)$bolsa->BolsaActual;


                if ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                    if ($BolsaActual >= $bolsaAt) {
                        $bolsatotal = new BolsaTotal();
                        $bolsatotal->BNF_TipoPaquete_id = $tipopaquete;
                        $bolsatotal->BNF_Empresa_id = $BNF_Empresa_id;
                        $bolsatotal->BolsaActual = $BolsaActual + ((int)$paqueteprov->Bolsa - $bolsaAt);
                        if ($bolsa->BolsaActual == 'NULL') {
                            $this->getBolsaTable()->saveBolsa($bolsatotal);
                        } else {
                            $this->getBolsaTable()->editBolsa($bolsatotal);
                        }
                    } else {
                        $mesage = true;
                    }
                }
                //////////
                if ($mesage) {
                    $this->flashMessenger()->addMessage('La Bolsa del Paquete ya ha sido Utilizada');
                    $alert = 'warning';
                } else {
                    $this->flashMessenger()->addMessage('Asignacion Modificada Correctamente');
                }
                return $this->redirect()->toRoute('paquete');
            }
        }
        $detail = null;
        $tipodes = null;
        if ($paqueteprov->BNF_TipoPaquete_id == $this::TIPO_PAQUETE_DESCARGA) {
            $tipodes = 'Descarga';
            $des = 'Descargas';
            if ($paqueteprov->CantidadDescargas == 1) {
                $des = 'Descarga';
            }
            $detail = $paqueteprov->CantidadDescargas .
                ' ' . $des . ' - S/' . $paqueteprov->PrecioUnitarioDescarga .
                ' por Descarga - ' . $paqueteprov->Bonificacion .
                ' Bonificación - S/' . $paqueteprov->PrecioUnitarioBonificacion .
                ' por Bonificación ';
        } elseif ($paqueteprov->BNF_TipoPaquete_id == $this::TIPO_PAQUETE_PRESENCIA) {
            $tipodes = 'Presencia';
            $detail = 'Costo x Día: S/' . $paqueteprov->CostoDia .
                ' Número de Días: ' . $paqueteprov->NumeroDias;
        } elseif ($paqueteprov->BNF_TipoPaquete_id == $this::TIPO_PAQUETE_LEAD) {
            $tipodes = 'Lead';
            $detail = 'Costo x Lead ' . $paqueteprov->CostoPorLead .
                ' Máximo de Leads ' . $paqueteprov->MaximoLeads;
        }

        $form->get('TipoPaquete')->setValue($tipodes);
        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paquete' => 'active',
                'passing' => 'active',
                'id' => $id,
                'form' => $form,
                'tipo' => $tipodes,
                'msg' => $msg,
                'alert' => $alert,
                'errors' => $errors,
                'detail' => $detail
            )
        );
    }

    public function deleteassingAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $val = $this->getRequest()->getPost('val');
        $id = $this->getRequest()->getPost('id');
        $eliminado = array();

        $paquetesprovs = $this->getPaqueteProvTable()->getPaqueteProvEdit($id);
        foreach ($paquetesprovs as $dato) {
            $paqueteprov = $dato;
            $eliminado[] = $paqueteprov->TEliminado;
        }

        if ($eliminado[0] == 0) {
            $BNF_Empresa_id = $paqueteprov->BNF_Empresa_id;
            $tipopaquete = $paqueteprov->BNF_TipoPaquete_id;

            ///verifica si esta asignadoa una oferta
            $verificar = $this->getOfertaTable()->getOfertaExitsXTipoPaqueteEmpresa($BNF_Empresa_id, $tipopaquete);
            if ($verificar) {
                ///eliminar paqueteasignado
                $this->getPaqueteProvTable()->deletePaqueteProv($id, $val);

                ////////eliminar bolsa de paquete asignado
                $bolsa = $this->getBolsaTable()
                    ->getBolsaxEmprexTipo($BNF_Empresa_id, $tipopaquete);
                $BolsaActual = (int)$bolsa->BolsaActual;

                //var_dump($bolsa->BolsaActual);exit;

                $bolsatotal = new BolsaTotal();
                $bolsatotal->BNF_TipoPaquete_id = $tipopaquete;
                $bolsatotal->BNF_Empresa_id = $BNF_Empresa_id;
                if ($val == 1) {
                    if ($tipopaquete == $this::TIPO_PAQUETE_DESCARGA) {
                        $bolsatotal->BolsaActual = $BolsaActual - (
                                (int)$paqueteprov->CantidadDescargas + (int)$paqueteprov->Bonificacion);
                    } elseif ($tipopaquete == $this::TIPO_PAQUETE_PRESENCIA) {
                        $bolsatotal->BolsaActual = $BolsaActual - ((int)$paqueteprov->NumeroDias);
                    } elseif ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                        $bolsatotal->BolsaActual = $BolsaActual - ((int)$paqueteprov->MaximoLeads);
                    }
                } else {
                    if ($tipopaquete == $this::TIPO_PAQUETE_DESCARGA) {
                        $bolsatotal->BolsaActual = $BolsaActual + (
                                (int)$paqueteprov->CantidadDescargas + (int)$paqueteprov->Bonificacion);
                    } elseif ($tipopaquete == $this::TIPO_PAQUETE_PRESENCIA) {
                        $bolsatotal->BolsaActual = $BolsaActual + ((int)$paqueteprov->NumeroDias);
                    } elseif ($tipopaquete == $this::TIPO_PAQUETE_LEAD) {
                        $bolsatotal->BolsaActual = $BolsaActual + ((int)$paqueteprov->MaximoLeads);
                    }
                }

                if ($bolsa->BolsaActual == 'NULL') {
                    $this->getBolsaTable()->saveBolsa($bolsatotal);
                } else {
                    $this->getBolsaTable()->editBolsa($bolsatotal);
                }

                $bolsa = $this->getBolsaTable()
                    ->getBolsaxEmprexTipo($BNF_Empresa_id, $tipopaquete);
                $response->setContent(
                    Json::encode(
                        array(
                            'type' => '2',
                            'bolsa' => (int)$bolsa->BolsaActual)
                    )
                );
            } else {
                $response->setContent(
                    Json::encode(
                        array(
                            'type' => '1',
                            'message' => 'El Paquete ya a sido asignado a una Oferta'
                        )
                    )
                );
            }
        }

        return $response;
    }

    public function listAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        /////////
        $busqueda = array(
            'Paquete' => 'Nombre',
            'TipoPaquete' => 'NombreTipoPaquete',
            'Bolsa' => 'Bolsa',
            'Activo' => 'Eliminado',
            'Precio' => 'Precio',
            'Cantidad' => 'Cantidad'
        );

        $datos = new BuscarPaqueteData($this);

        $form = new BuscarPaqueteForm('paquete', $datos->getFormData());
        $paquetes = null;
        $pais = 0;
        $tipo = 0;
        $nombre = null;
        $inicio = null;
        $fin = null;

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];

        } else {
            $order_by_o = 'id';
            $order_by = 'FechaCreacion';
        }

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $this->getRequest()->getPost()->toArray();
            $validate = new BuscarPaqueteFilter();
            $form->setInputFilter(
                $validate->getInputFilter($datos->getFilterData(), $post)
            );

            $form->setData($post);

            if ($form->isValid()) {
                $pais = (!empty($request->getPost()->NombrePais))
                    ? $request->getPost()->NombrePais : 0;
                $tipo = (!empty($request->getPost()->TipoPaquete))
                    ? $request->getPost()->TipoPaquete : 0;
                $inicio = (!empty($request->getPost()->FechaInicio))
                    ? $request->getPost()->FechaInicio : null;
                $fin = (!empty($request->getPost()->FechaFin))
                    ? $request->getPost()->FechaFin : null;
            }
        } else {
            $pais = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : 0;
            $tipo = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : 0;
            $inicio = $this->params()->fromRoute('q3') ? $this->params()->fromRoute('q3') : null;
            $fin = $this->params()->fromRoute('q4') ? $this->params()->fromRoute('q4') : null;

            $validator = new Date(array('format' => 'Y-m-d'));

            if ($inicio) {
                if (!$validator->isValid($inicio)) {
                    $this->getResponse()->setStatusCode(404);
                    return;
                }
            }

            if ($fin) {
                if (!$validator->isValid($fin)) {
                    $this->getResponse()->setStatusCode(404);
                    return;
                }
            }

            $form->setData(array(
                "NombrePais" => $pais,
                "TipoPaquete" => $tipo,
                "FechaInicio" => $inicio,
                "FechaFin" => $fin));
        }

        $paquetes = $this->getPaqueteTable()->getDetailPaquete($pais, $tipo, $inicio, $fin, $order_by, $order);
        $paginator = new Paginator(new paginatorIterator($paquetes, $order_by));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'paquete' => 'active',
                'plistar' => 'active',
                'form' => $form,
                'paquetes' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'q1' => $pais,
                'q2' => $tipo,
                'q3' => $inicio ? $inicio : 0,
                'q4' => $fin ? $fin : 0,
                'p' => $page,
            )
        );
    }

    public function exportPaqueteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultado = $this->getPaqueteTable()->getExportPaquete();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();

        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Paquetes")
                ->setSubject("Paquetes")
                ->setDescription("Documento listando las Paquetes")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Paquetes");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:O' . (int)$registros);
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
            $objPHPExcel->getActiveSheet()->getStyle('A1:O' . ((int)$registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Nombre del Paquete')
                ->setCellValue('C1', 'Tipo de Paquete')
                ->setCellValue('D1', 'Precio')
                ->setCellValue('E1', 'Cantidad de Descargas')
                ->setCellValue('F1', 'Precio Unitario por Descarga')
                ->setCellValue('G1', 'Bonificación')
                ->setCellValue('H1', 'Precio Unitario por Bonificación')
                ->setCellValue('I1', 'Numero de Días')
                ->setCellValue('J1', 'Costo por Día')
                ->setCellValue('K1', 'Bolsa')
                ->setCellValue('L1', 'País')
                ->setCellValue('M1', 'Fecha de Creación')
                ->setCellValue('N1', 'Fecha de Actualización')
                ->setCellValue('O1', 'Eliminado');

            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Nombre)
                    ->setCellValue('C' . $i, $registro->NombreTipoPaquete)
                    ->setCellValue('D' . $i, $registro->Precio)
                    ->setCellValue('E' . $i, $registro->CantidadDescargas)
                    ->setCellValue('F' . $i, $registro->PrecioUnitarioDescarga)
                    ->setCellValue('G' . $i, $registro->Bonificacion)
                    ->setCellValue('H' . $i, $registro->PrecioUnitarioBonificacion)
                    ->setCellValue('I' . $i, $registro->NumeroDias)
                    ->setCellValue('J' . $i, $registro->CostoDia)
                    ->setCellValue('K' . $i, $registro->Bolsa)
                    ->setCellValue('L' . $i, $registro->NombrePais)
                    ->setCellValue('M' . $i, $registro->FechaCreacion)
                    ->setCellValue('N' . $i, $registro->FechaActualizacion)
                    ->setCellValue('O' . $i, ((int)$registro->Eliminado == $this::ESTADO_PAQUETE_ELIMINADO)
                        ? 'Inactivo' : 'Activo');
                $i++;

            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Paquetes.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
