<?php

namespace Premios\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Premios\Form\BuscarAsignacionPremios;
use Premios\Form\FormAsignarPremios;
use Premios\Model\AsignacionPremios;
use Premios\Model\AsignacionPremiosEstadoLog;
use Zend\I18n\Validator\IsInt;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Digits;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class AsignacionesController extends AbstractActionController
{
    #region Constantes
    const MESSAGE_ERROR = "Ocurrió un error al procesar los datos ingresados";
    const MESSAGE_ERROR_PRESUPUESTO = "La cantidad de premios ingresada es mayor al presupuesto del Segmento";
    const MESSAGE_SUCCESS = "Premios Asignados Correctamente";

    const TIPO_MESSAGE_CLASICO = "Clasico";
    const TIPO_MESSAGE_PERSONALIZADO = "Personalizado";

    const ESTADO_PREMIOS_ACTIVO = "Activado";
    const ESTADO_PREMIOS_DESACTIVADO = "Desactivado";

    const ACTION_PERSONALIZED_INPUT = "input";
    const ACTION_PERSONALIZED_FILE = "file";

    const OPERACION_ASIGNAR = "Asignar";
    const OPERACION_SUMAR = "Sumar";
    const OPERACION_RESTAR = "Restar";

    const USUARIO_CLIENTE = 7;
    #endregion

    #region ObjectTables
    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosTable');
    }

    public function getSegmentosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\SegmentosPremiosTable');
    }

    public function getCampaniaEmpresaTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosEmpresasTable');
    }

    public function getAsignacionPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosTable');
    }

    public function getEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaClienteClienteTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\ClienteTable');
    }

    public function getAsignacionPremiosEstadoLogTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosEstadoLogTable');
    }
    #endregion

    #region Inicializando Data
    public function inicializacion()
    {
        $dataEmpresa = array();
        $filterEmpresa = array();

        $empresas = $this->getSegmentosTable()->getListaDetalleAsignacion('id', 'asc');
        try {
            $temp = array();
            foreach ($empresas as $empresa) {
                if (!in_array($empresa->id, $temp )){
                    $dataEmpresa[$empresa->EmpresaId] = $empresa->EmpresaFull;
                    $filterEmpresa[$empresa->EmpresaId] = [$empresa->EmpresaId];
                    array_push($temp, $empresa->EmpresaId );
                }
            }
        } catch (\Exception $ex) {
            $dataEmpresa = array();
        }

        $formulario['empresa'] = $dataEmpresa;
        $filtro['empresa'] = array_keys($filterEmpresa);

        return array($formulario, $filtro);
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $asignaciones = null;
        $campania = null;
        $presupuesto = null;
        $inicio = null;
        $final = null;
        $activo = null;
        $nombre_empresa = "";

        $busqueda = array(
            'Empresa' => 'NombreComercial',
            'Campania' => 'NombreCampania',
            'Segmento' => 'NombreSegmento',
            'Presupuesto' => 'Subtotal',
            'Asignado' => 'AsignadoActivo',
            'Disponible' => 'DisponibleAsignar',
            'Estado' => 'EstadoCampania',
            'Tipo' => 'TipoSegmento'
        );

        $data = $this->inicializacion();

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            if (isset($data[0]['empresa'][$empresa_value])) {
                $nombre_empresa = $data[0]['empresa'][$empresa_value];
            } else {
                $nombre_empresa = $this->getEmpresaTable()->getEmpresa($empresa_value)->NombreComercial;
            }
            //$form = new BuscarAsignacionPremios('buscar', $empresa_value, $tipo_usuario);
            $form = new BuscarAsignacionPremios('buscar', $data[0], $tipo_usuario);
        } else {
            $form = new BuscarAsignacionPremios('buscar asignaciones', $data[0]);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $empresa = $request->getPost()->Empresas ? $request->getPost()->Empresas : null;
            $campania = $request->getPost()->Campania ? $request->getPost()->Campania : null;
        } else {
            $empresa = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : $empresa_value;
            $campania = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
        }

        $form->setData(
            array(
                'Empresas' => ($tipo_usuario == $this::USUARIO_CLIENTE) ? $empresa_value : $empresa,
                "Campania" => $campania
            )
        );

        //Determinar ordenamiento
        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];
        } else {
            $order_by_o = 'id';
            $order_by = 'BNF3_Campanias.FechaCreacion';
        }

        //Se obtiene los datos filtrados y la paginacion segun el orden
        $asignaciones = $this->getSegmentosTable()->getListaDetalleAsignacion($order_by, $order, $empresa, $campania);
        $paginator = new Paginator(new paginatorIterator($asignaciones, $order_by));
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($itemsPerPage)->setPageRange(7);

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'asigpremios' => 'active',
                'asignaciones' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'form' => $form,
                'p' => $page,
                'q1' => $empresa,
                'q2' => $campania,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }

    public function clasicoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $successMessage = null;
        $fileMessage = array();
        $alertMessage = array();
        $errorMessageCsv = false;
        $errorCsv = false;
        $errorValid = false;
        $countValid = 0;

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('asignaciones-premios', array('action' => 'index'));
        }

        try {
            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
            $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
            $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);

            $totalAsignado = $this->getAsignacionPremiosTable()->getTotalAssigned($id)->TotalAsignados;
            $totalUsuariosAsignados = $this->getAsignacionPremiosTable()->getTotalUsers($id)->TotalUsuarios;

            $tipoSegmento = $datoCampania->TipoSegmento == "Clasico" ? "Clásico" : "Clasico";
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('asignaciones-premios', array('action' => 'index'));
        }

        $opcion = ((int)$datoSegmento->Eliminado) ? false : true;

        $form = new FormAsignarPremios();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $fileData = $this->params()->fromFiles('file_csv');

            $sizeValidator = new Size(array('min' => '1kB', 'max' => '2MB'));
            $extensionValidator = new Extension(array('extension' => array('xls', 'xlsx')), true);

            if (!$sizeValidator->isValid($fileData) || !$extensionValidator->isValid($fileData)) {
                if ($fileData['name'] == '') {
                    $fileMessage = 'Debe seleccionar un archivo.';
                } else {
                    $fileMessage = 'Archivo no válido.';
                }
            } else {
                $inputFileName = $fileData['tmp_name'];
                $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load($inputFileName);

                $objWorksheet = $objPHPExcel->setActiveSheetIndex();
                $highestColumn = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                $totalRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                $dataCsv = array();
                if ($highestColumn == "A") {
                    $maximoUsuarios = $datoSegmento->CantidadPersonas;
                    $totalUsuDisponible = $maximoUsuarios - $totalUsuariosAsignados;
                    if (($totalRows - 1) <= $maximoUsuarios) {
                        if (($totalRows - 1) <= $totalUsuDisponible) {
                            foreach ($objWorksheet->getRowIterator(2) as $row) {
                                $cellIterator = $row->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(false);
                                $errorRepeat = false;
                                $rowIndex = $row->getRowIndex();
                                $dataCell = array();
                                foreach ($cellIterator as $cell) {
                                    array_push($dataCell, (string)$cell->getValue());
                                }
                                $registro = "Registro #" . ($rowIndex - 1) . ". ";
                                if ($dataCell[0] == '') {
                                    $errorValid = true;
                                    $errorMessageCsv[$rowIndex] = $registro . 'Falta ingresar número de documento.';
                                } else {
                                    if (array_key_exists($dataCell[0], $dataCsv)) {
                                        $errorRepeat = true;
                                        $alertMessage[] = $registro . 'Documento repetido.';
                                    } else {
                                        $valid = new  Regex(array('pattern' => "/^([a-zA-Z0-9]+(\-)?(\/)?)+$/"));
                                        if ($valid->isValid($dataCell[0])) {
                                            if ($this->getEmpresaClienteTable()
                                                ->searchEmpresaClientebyDoc($datoEmpresa->id, $dataCell[0])
                                            ) {
                                                if (!$this->getEmpresaClienteTable()
                                                    ->searchEmpresaClienteActive($datoEmpresa->id, $dataCell[0])
                                                ) {
                                                    $errorValid = true;
                                                    $errorMessageCsv[$rowIndex] = "El documento en la fila "
                                                        . $registro . " no está activo";
                                                } else {
                                                    $dataAsignado = $this->getAsignacionPremiosTable()
                                                        ->searchDocument($datoEmpresa->id, $datoCampania->id, $dataCell[0]);
                                                    if ($dataAsignado) {
                                                        $errorValid = true;
                                                        $errorMessageCsv[$rowIndex] = "El documento en la fila "
                                                            . $registro . " ya cuenta con una asignación";
                                                    }
                                                }
                                            } else {
                                                $errorValid = true;
                                                $errorMessageCsv[$rowIndex] = "El documento en la fila " . $registro
                                                    . " no está registrado, ningún premio fue asignado";
                                            }
                                        } else {
                                            $errorValid = true;
                                            $errorMessageCsv[$rowIndex] = $registro .
                                                'El documento ingresado no es un documento válido.';
                                        }
                                    }
                                }

                                if ($errorRepeat == false) {
                                    $dataCsv[$dataCell[0]] = $dataCell;
                                }
                            }
                        } else {
                            $errorCsv = true;
                            $errorMessageCsv[] = 'La cantidad de registros es mayor a ' .
                                'la cantidad de Usuarios Disponibles para asignar ('
                                . $totalUsuDisponible . ' usuarios por asignar)';
                        }
                    } else {
                        $errorCsv = true;
                        $errorMessageCsv[] = 'La cantidad de registros es mayor a ' .
                            'la cantidad máxima de Usuarios.';
                    }
                } else {
                    $errorCsv = true;
                    $errorMessageCsv[] = 'Formato del documento incorrecto, por favor revise la plantilla.';
                }

                if (!$errorCsv && !$errorValid) {
                    $totalPresupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo -
                        $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;
                    $premios = $datoSegmento->CantidadPremios;

                    foreach ($dataCsv as $value) {
                        $dataCliente = $this->getClienteTable()->getClientByDoc($value);

                        $asignacion = new AsignacionPremios();
                        $asignacion->BNF3_Segmento_id = $datoSegmento->id;
                        $asignacion->BNF_Cliente_id = $dataCliente->id;
                        $asignacion->CantidadPremios = $premios;
                        $asignacion->CantidadPremiosDisponibles = $premios;
                        $asignacion->EstadoPremios = 'Activado';
                        $asignacion->Eliminado = 0;

                        $asignacionId = $this->getAsignacionPremiosTable()->saveAsignacion($asignacion);

                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $asignacionId;
                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $AsignacionPremiosEstadoLog->CantidadPremios = $asignacion->CantidadPremios;
                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = 0;
                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = $asignacion->CantidadPremiosDisponibles;
                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = 0;
                        $AsignacionPremiosEstadoLog->EstadoPremios = $asignacion->EstadoPremios;
                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                        $AsignacionPremiosEstadoLog->Premios = $asignacion->CantidadPremios;
                        $AsignacionPremiosEstadoLog->Motivo = "Creando Asignación";
                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                        $countValid++;

                        $totalPresupuesto = $totalPresupuesto - $datoSegmento->CantidadPremios;
                        if ($totalPresupuesto >= $datoSegmento->CantidadPremios) {
                            $premios = $datoSegmento->CantidadPremios;
                        } else {
                            $premios = $totalPresupuesto;
                        }
                    }

                    $datoSegmento = $this->getSegmentosTable()->getSegmentosPremios($id);
                    $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
                    $porcentaje = $this->getAsignacionPremiosTable()->getTotalAssignedByCampaign($datoCampania->id);
                    $total = $porcentaje->TotalAsignados;
                    if ($total >= $datoCampania->ParametroAlerta && !empty($datoCampania->ParametroAlerta)) {
                        $config = $this->getServiceLocator()->get('Config');
                        $email = $config['email_sender']['asignacion'];
                        $this->enviarCorreoAdmin($datoCampania, $total, $email);
                    }

                    $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
                    $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
                    $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
                    $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
                    $totalAsignado = $this->getAsignacionPremiosTable()->getTotalAssigned($id)->TotalAsignados;
                    $totalUsuariosAsignados = $this->getAsignacionPremiosTable()->getTotalUsers($id)->TotalUsuarios;
                    $successMessage = 'Se registraron un total de ' . $countValid . ' Usuarios Finales.';
                }
            }
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'asigpremios' => 'active',
                'id' => $id,
                'form' => $form,
                'opcion' => $opcion,
                'segmento' => $datoSegmento,
                'campania' => $datoCampania->NombreCampania,
                'estado_campania' => $datoCampania->EstadoCampania,
                'tipoSegmento' => $tipoSegmento,
                'empresa' => $datoEmpresa->NombreComercial,
                'totalAsignado' => $totalAsignado,
                'totalPremios' => $totalAsignado,
                'totalUsuarios' => $totalUsuariosAsignados,
                'fileMessage' => $fileMessage,
                'errorMessageCsv' => $errorMessageCsv,
                'alertMessage' => $alertMessage,
                'successMessage' => $successMessage,
            )
        );
    }

    public function personalizadoAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $confirm = array();
        $type = "danger";
        $numeroDocumento = "";
        $sumaPremios = "";
        $restaPremios = "";
        $premiosAsignados = "";
        $estadoAsignacion = "";
        $numeroDocumentoMessage = "";
        $sumaPremiosMessage = "";
        $restaPremiosMessage = "";

        $successMessage = null;
        $fileMessage = array();
        $alertMessage = array();
        $errorMessageCsv = false;
        $errorCsv = false;
        $errorValid = false;
        $countValid = 0;
        $total = 0;
        $totalAplicar = 0;
        $totalDatos = 0;

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('asignaciones-premios', array('action' => 'index'));
        }

        try {
            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
            $pres = $this->getCampaniaTable()->getPresupuestoAcumulado($datoSegmento->BNF3_Campania_id);
            $datoCampania->PresupuestoAsignado = $pres->PresupuestoAsignado;
            $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
            $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
            $dataAsignados = $this->getAsignacionPremiosTable()->getPersonalizedAssigned($id);

            $datos = array();
            foreach ($dataAsignados as $data) {
                $totalDatos++;
                $datos[0][] = $data->BNF_Cliente_id;
                $datos[1][] = (int)$data->CantidadPremios;
                $datos[2][] = $data->EstadoPremios;
                $totalAplicar = $totalAplicar + $data->CantidadPremios;
            }

            if (!empty($datos)) {
                $numeroDocumento = $this->generarArreglosJS($datos[0]);
                $premiosAsignados = $this->generarArreglosJS($datos[1]);
                $estadoAsignacion = $this->generarArreglosJS($datos[2]);
            }

            $presupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo
                - $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('asignaciones-premios', array('action' => 'index'));
        }

        $opcion = ((int)$datoSegmento->Eliminado) ? false : true;

        $form = new FormAsignarPremios();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $action = $request->getPost()->action;
            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
            $presupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo
                - $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;

            if ($action == $this::ACTION_PERSONALIZED_INPUT) {
                $resultados = $this->validarCampos($request);
                $approved = $resultados[0];
                $message = $resultados[1];

                if ($approved) {
                    $dataDocumentos = $request->getPost()->numeroDocumento;
                    $dataSumaPremios = $request->getPost()->sumaPremios;
                    $dataRestaPremios = $request->getPost()->restaPremios;
                    $dataEstadoPremios = $request->getPost()->estado;
                    $totalDatos = count($dataDocumentos);

                    //Validar total de premio
                    $totalPremios = 0;
                    foreach ($dataSumaPremios as $premio) {
                        $totalPremios = $totalPremios + $premio;
                    }

                    if ($totalPremios <= $presupuesto) {
                        #region Validar Documentos
                        $listaDocumentos = array();
                        $documentoEstado = true;
                        foreach ($dataDocumentos as $documento) {
                            if (!in_array($documento, $listaDocumentos)) {
                                if ($this->getEmpresaClienteTable()
                                    ->searchEmpresaClientebyDoc($datoEmpresa->id, $documento)
                                ) {
                                    if (!$this->getEmpresaClienteTable()
                                        ->searchEmpresaClienteActive($datoEmpresa->id, $documento)
                                    ) {
                                        $documentoEstado = false;
                                        $confirm[] = "El documento " . $documento . " no está activo";
                                        $type = "danger";
                                    } else {
                                        if ($this->getAsignacionPremiosTable()
                                            ->getPersonalizedAssignedDisabled($datoSegmento->id, $documento)
                                        ) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $documento . " tiene una asignación desactivada";
                                            $type = "danger";
                                        }
                                    }
                                } else {
                                    $documentoEstado = false;
                                    $confirm[] = "El documento " . $documento . " no está registrado, ningún premio fue asignado";
                                    $type = "danger";
                                }
                                $listaDocumentos[] = $documento;
                            } else {
                                $documentoEstado = false;
                                $confirm[] = "El documento " . $documento . " está repetido";
                                $type = "danger";
                            }
                        }
                        #endregion

                        #region Validar Premios
                        if ($documentoEstado) {
                            foreach ($dataDocumentos as $key => $documento) {
                                $dataCliente = $this->getClienteTable()->getClientByDoc($documento);
                                $dataAsignacion = $this->getAsignacionPremiosTable()
                                    ->getAsignacionCliente($id, $dataCliente->id);

                                if ($dataAsignacion) {
                                    if ((int)$dataAsignacion->Eliminado == 0) {
                                        $resultado = $dataAsignacion->CantidadPremios + (int)$dataSumaPremios[$key] - (int)$dataRestaPremios[$key];
                                        $premiosUsados = $dataAsignacion->CantidadPremiosUsados;

                                        if ($resultado < $premiosUsados) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $documento . " no se le puede restar una cantidad " .
                                                "mayor a los premios usados";
                                            $type = "danger";
                                        } else {
                                            if ($resultado < 0) {
                                                $documentoEstado = false;
                                                $confirm[] = "El documento " . $documento . " no se le puede restar una " .
                                                    "cantidad mayor a sus premios asignados";
                                                $type = "danger";
                                            }
                                        }
                                    } else {
                                        if ((int)$dataRestaPremios[$key] > 0) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $documento .
                                                " esta cancelado y no se puede restar premios";
                                            $type = "danger";
                                        }
                                    }
                                } else {
                                    if ((int)$dataSumaPremios[$key] == 0 && (int)$dataRestaPremios[$key] == 0) {
                                        $documentoEstado = false;
                                        $confirm[] = "No se ingresó una cantidad de premios para el documento " . $documento;
                                        $type = "danger";
                                    } elseif ((int)$dataSumaPremios[$key] == 0 && (int)$dataRestaPremios[$key] > 0) {
                                        $documentoEstado = false;
                                        $confirm[] = "El documento " . $documento . " no tiene ningún premio asignado para restar";
                                        $type = "danger";
                                    }
                                }
                            }
                        }
                        #endregion

                        #region Asignacion de Premios
                        if ($documentoEstado) {
                            foreach ($dataDocumentos as $key => $value) {
                                $dataCliente = $this->getClienteTable()->getClientByDoc($value);
                                $dataAsignacion = $this->getAsignacionPremiosTable()
                                    ->getAsignacionCliente($id, $dataCliente->id);

                                if ((int)$dataSumaPremios[$key] > 0) {
                                    if ($dataAsignacion) {
                                        if ($dataAsignacion->EstadoPremios == "Cancelado") {
                                            $dataAsignacion->EstadoPremios = "Activado";
                                            $motivo = "Agregando Premios después de Cancelación";
                                        } else {
                                            $motivo = "Agregando Premios";
                                        }

                                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $dataAsignacion->id;
                                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $dataAsignacion->BNF3_Segmento_id;
                                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $AsignacionPremiosEstadoLog->CantidadPremios = $dataAsignacion->CantidadPremios + (int)$dataSumaPremios[$key];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = (int)$dataAsignacion->CantidadPremiosUsados;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = $dataAsignacion->CantidadPremiosDisponibles + (int)$dataSumaPremios[$key];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = (int)$dataAsignacion->CantidadPremiosEliminados;
                                        $AsignacionPremiosEstadoLog->EstadoPremios = $dataAsignacion->EstadoPremios;
                                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_SUMAR;
                                        $AsignacionPremiosEstadoLog->Premios = (int)$dataSumaPremios[$key];
                                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $AsignacionPremiosEstadoLog->Motivo = $motivo;
                                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                                        $dataAsignacion->BNF3_Segmento_id = $id;
                                        $dataAsignacion->CantidadPremios =
                                            $dataAsignacion->CantidadPremios + (int)$dataSumaPremios[$key];
                                        $dataAsignacion->CantidadPremiosDisponibles =
                                            $dataAsignacion->CantidadPremiosDisponibles + (int)$dataSumaPremios[$key];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionPremiosTable()->saveAsignacion($dataAsignacion);
                                    } else {
                                        $asignacion = new AsignacionPremios();
                                        $asignacion->BNF3_Segmento_id = $id;
                                        $asignacion->BNF_Cliente_id = $dataCliente->id;
                                        $asignacion->CantidadPremios = (int)$dataSumaPremios[$key];
                                        $asignacion->CantidadPremiosDisponibles = (int)$dataSumaPremios[$key];
                                        $asignacion->EstadoPremios = 'Activado';
                                        $asignacion->Eliminado = 0;
                                        $asignacionId = $this->getAsignacionPremiosTable()->saveAsignacion($asignacion);

                                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $asignacionId;
                                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                        $AsignacionPremiosEstadoLog->CantidadPremios = $asignacion->CantidadPremios;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = 0;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = $asignacion->CantidadPremiosDisponibles;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = 0;
                                        $AsignacionPremiosEstadoLog->EstadoPremios = $asignacion->EstadoPremios;
                                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                                        $AsignacionPremiosEstadoLog->Premios = $asignacion->CantidadPremios;
                                        $AsignacionPremiosEstadoLog->Motivo = "Creando Asignación";
                                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);
                                    }
                                }

                                if ((int)$dataRestaPremios[$key] > 0) {
                                    if ($dataAsignacion) {
                                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $dataAsignacion->id;
                                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $dataAsignacion->BNF3_Segmento_id;
                                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $AsignacionPremiosEstadoLog->CantidadPremios =
                                            $dataAsignacion->CantidadPremios - (int)$dataRestaPremios[$key];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = (int)$dataAsignacion->CantidadPremiosUsados;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles =
                                            $dataAsignacion->CantidadPremiosDisponibles - (int)$dataRestaPremios[$key];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = (int)$dataAsignacion->CantidadPremiosEliminados;
                                        $AsignacionPremiosEstadoLog->EstadoPremios = $dataAsignacion->EstadoPremios;
                                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_RESTAR;
                                        $AsignacionPremiosEstadoLog->Premios = (int)$dataRestaPremios[$key];
                                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $AsignacionPremiosEstadoLog->Motivo = "Restando Premios";
                                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                                        $dataAsignacion->BNF3_Segmento_id = $id;
                                        $dataAsignacion->BNF_Cliente_id = $dataCliente->id;
                                        $dataAsignacion->CantidadPremios =
                                            $dataAsignacion->CantidadPremios - (int)$dataRestaPremios[$key];
                                        $dataAsignacion->CantidadPremiosDisponibles =
                                            $dataAsignacion->CantidadPremiosDisponibles - (int)$dataRestaPremios[$key];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionPremiosTable()->saveAsignacion($dataAsignacion);
                                    }
                                }
                            }

                            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
                            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
                            $pres = $this->getCampaniaTable()->getPresupuestoAcumulado($datoSegmento->BNF3_Campania_id);
                            $datoCampania->PresupuestoAsignado = $pres->PresupuestoAsignado;
                            $porcentaje = $this->getAsignacionPremiosTable()->getTotalAssignedByCampaign($datoCampania->id);
                            $total = $porcentaje->TotalAsignados;

                            if ($total >= $datoCampania->ParametroAlerta && !empty($datoCampania->ParametroAlerta)) {
                                $config = $this->getServiceLocator()->get('Config');
                                $email = $config['email_sender']['asignacion'];
                                $this->enviarCorreoAdmin($datoCampania, $total, $email);
                            }

                            $sumaPremios = "";
                            $restaPremios = "";
                            $confirm[] = $this::MESSAGE_SUCCESS;
                            $type = "success";

                            $dataAsignados = $this->getAsignacionPremiosTable()->getPersonalizedAssigned($id);

                            $datos = array();
                            $totalDatos = 0;
                            foreach ($dataAsignados as $data) {
                                $totalDatos++;
                                $datos[0][] = $data->BNF_Cliente_id;
                                $datos[1][] = (int)$data->CantidadPremios;
                                $datos[2][] = $data->EstadoPremios;
                                $totalAplicar = $totalAplicar + $data->CantidadPremios;
                            }

                            if (!empty($datos)) {
                                $numeroDocumento = $this->generarArreglosJS($datos[0]);
                                $premiosAsignados = $this->generarArreglosJS($datos[1]);
                                $estadoAsignacion = $this->generarArreglosJS($datos[2]);
                            }

                            $presupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo
                                - $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;
                        } else {
                            //Datos
                            $numeroDocumento = $this->generarArreglosJS($request->getPost()->numeroDocumento);
                            $sumaPremios = $this->generarArreglosJS($request->getPost()->sumaPremios);
                            $restaPremios = $this->generarArreglosJS($request->getPost()->restaPremios);
                            $estadoAsignacion = $this->generarArreglosJS($request->getPost()->estado);
                            //Mensajes de Error
                            $numeroDocumentoMessage = $this->generarArreglosJS($message["numeroDocumento"]);
                            $sumaPremiosMessage = $this->generarArreglosJS($message["sumaPremios"]);
                            $restaPremiosMessage = $this->generarArreglosJS($message["restaPremios"]);
                        }
                        #endregion
                    } else {
                        //Datos
                        $numeroDocumento = $this->generarArreglosJS($request->getPost()->numeroDocumento);
                        $sumaPremios = $this->generarArreglosJS($request->getPost()->sumaPremios);
                        $restaPremios = $this->generarArreglosJS($request->getPost()->restaPremios);
                        $estadoAsignacion = $this->generarArreglosJS($request->getPost()->estado);
                        //Mensajes de Error
                        $numeroDocumentoMessage = $this->generarArreglosJS($message["numeroDocumento"]);
                        $sumaPremiosMessage = $this->generarArreglosJS($message["sumaPremios"]);
                        $restaPremiosMessage = $this->generarArreglosJS($message["restaPremios"]);
                        $confirm[] = $this::MESSAGE_ERROR_PRESUPUESTO;
                        $type = "danger";
                    }
                } else {
                    //Datos
                    $numeroDocumento = $this->generarArreglosJS($request->getPost()->numeroDocumento);
                    $sumaPremios = $this->generarArreglosJS($request->getPost()->sumaPremios);
                    $restaPremios = $this->generarArreglosJS($request->getPost()->restaPremios);
                    $estadoAsignacion = $this->generarArreglosJS($request->getPost()->estado);
                    //Mensajes de Error
                    $numeroDocumentoMessage = $this->generarArreglosJS($message["numeroDocumento"]);
                    $sumaPremiosMessage = $this->generarArreglosJS($message["sumaPremios"]);
                    $restaPremiosMessage = $this->generarArreglosJS($message["restaPremios"]);
                    $confirm[] = $this::MESSAGE_ERROR;
                    $type = "danger";
                }
            } elseif ($action == $this::ACTION_PERSONALIZED_FILE) {
                $fileData = $this->params()->fromFiles('file_csv');

                $sizeValidator = new Size(array('min' => '1kB', 'max' => '2MB'));
                $extensionValidator = new Extension(array('extension' => array('xls', 'xlsx')), true);

                if (!$sizeValidator->isValid($fileData) || !$extensionValidator->isValid($fileData)) {
                    if ($fileData['name'] == '') {
                        $fileMessage = 'Debe seleccionar un archivo.';
                    } else {
                        $fileMessage = 'Archivo no válido.';
                    }
                } else {
                    $inputFileName = $fileData['tmp_name'];
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $objPHPExcel = $objReader->load($inputFileName);

                    $objWorksheet = $objPHPExcel->setActiveSheetIndex();
                    $highestColumn = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                    #region Validar Datos
                    $dataCsv = array();
                    if ($highestColumn == "C") {
                        foreach ($objWorksheet->getRowIterator(2) as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            $errorRepeat = false;
                            $rowIndex = $row->getRowIndex();
                            $dataCell = array();
                            foreach ($cellIterator as $cell) {
                                array_push($dataCell, (string)$cell->getValue());
                            }
                            $registro = "Registro #" . ($rowIndex - 1) . ". ";
                            if ($dataCell[0] == '') {
                                $errorValid = true;
                                $errorMessageCsv[$rowIndex] = $registro . 'Falta ingresar número de documento.';
                            } else {
                                if (array_key_exists($dataCell[0], $dataCsv)) {
                                    $errorRepeat = true;
                                    $alertMessage[] = $registro . 'Documento repetido.';
                                } else {
                                    $valid = new  Regex(array('pattern' => "/^([a-zA-Z0-9]+(\-)?(\/)?)+$/"));
                                    if ($valid->isValid($dataCell[0])) {
                                        if ($this->getEmpresaClienteTable()
                                            ->searchEmpresaClientebyDoc($datoEmpresa->id, $dataCell[0])
                                        ) {
                                            if (!$this->getEmpresaClienteTable()
                                                ->searchEmpresaClienteActive($datoEmpresa->id, $dataCell[0])
                                            ) {
                                                $errorValid = true;
                                                $errorMessageCsv[$rowIndex] = "El documento en la fila " . $registro
                                                    . " no está activo";
                                            } else {
                                                if ($this->getAsignacionPremiosTable()
                                                    ->getPersonalizedAssignedDisabled($id, $dataCell[0])
                                                ) {
                                                    $errorValid = true;
                                                    $errorMessageCsv[$rowIndex] = "El documento en la fila " . $registro
                                                        . " tiene una asignación desactivada";
                                                }
                                            }
                                        } else {
                                            $errorValid = true;
                                            $errorMessageCsv[$rowIndex] = "El documento en la fila " . $registro
                                                . " no está registrado, ningún premio fue asignado";
                                        }
                                    } else {
                                        $errorValid = true;
                                        $errorMessageCsv[$rowIndex] = $registro .
                                            'El documento ingresado no es un documento válido.';
                                    }
                                }
                            }

                            if ($errorRepeat == false) {
                                $validNumb = new Digits();
                                if ($validNumb->isValid($dataCell[1])) {
                                    $total = $total + $dataCell[1];
                                    if ($total > $presupuesto) {
                                        $errorValid = true;
                                        $errorMessageCsv[$rowIndex] = $registro .
                                            'el total de premios supera al presupuesto.';
                                        break;
                                    }
                                } else {
                                    $errorValid = true;
                                    $errorMessageCsv[$rowIndex] = $registro .
                                        'la cantidad ingresada no es valida.';
                                }

                                if (!$errorValid) {
                                    $dataCsv[$dataCell[0]] = $dataCell;
                                }
                            }
                        }
                    } else {
                        $errorCsv = true;
                        $errorMessageCsv[] = 'Formato del documento incorrecto, por favor revise la plantilla.';
                    }
                    #endregion

                    if (!$errorCsv && !$errorValid) {
                        $documentoEstado = true;
                        #region Validar Premios
                        foreach ($dataCsv as $value) {
                            $dataCliente = $this->getClienteTable()->getClientByDoc($value[0]);
                            $dataAsignacion = $this->getAsignacionPremiosTable()
                                ->getAsignacionCliente($id, $dataCliente->id);

                            if ($dataAsignacion) {
                                if ((int)$dataAsignacion->Eliminado == 0) {
                                    $resultado = $dataAsignacion->CantidadPremios + (int)$value[1] - (int)$value[2];
                                    $premiosUsados = $dataAsignacion->CantidadPremiosUsados;
                                    if ($resultado < $premiosUsados) {
                                        $documentoEstado = false;
                                        $confirm[] = "El documento " . $value[0] .
                                            " no se le puede restar una cantidad mayor a los premios usados";
                                        $type = "danger";
                                    } else {
                                        if ($resultado < 0) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $value[0] . " no se le puede restar una " .
                                                "cantidad mayor a sus premios asignados";
                                            $type = "danger";
                                        }
                                    }
                                } else {
                                    if ((int)$value[2] > 0) {
                                        $documentoEstado = false;
                                        $confirm[] = "El documento " . $value[0] .
                                            " esta cancelado y no se puede restar premios";
                                        $type = "danger";
                                    }
                                }
                            } else {
                                if ((int)$value[2] > 0) {
                                    $documentoEstado = false;
                                    $confirm[] = "El documento " . $value[0] . " no tiene ningún premio asignado";
                                    $type = "danger";
                                }
                            }
                        }
                        #endregion

                        #region Asignacion de Premios
                        if ($documentoEstado) {
                            foreach ($dataCsv as $value) {
                                $dataCliente = $this->getClienteTable()->getClientByDoc($value[0]);
                                $dataAsignacion = $this->getAsignacionPremiosTable()
                                    ->getAsignacionCliente($id, $dataCliente->id);

                                if ((int)$value[1] > 0) {
                                    if ($dataAsignacion) {
                                        if ($dataAsignacion->EstadoPremios == "Cancelado") {
                                            $dataAsignacion->EstadoPremios = "Activado";
                                            $motivo = "Agregando Premios después de Cancelación";
                                        } else {
                                            $motivo = "Agregando Premios";
                                        }

                                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $dataAsignacion->id;
                                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $dataAsignacion->BNF3_Segmento_id;
                                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $AsignacionPremiosEstadoLog->CantidadPremios = $dataAsignacion->CantidadPremios + (int)$value[1];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = (int)$dataAsignacion->CantidadPremiosUsados;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = $dataAsignacion->CantidadPremiosDisponibles + (int)$value[1];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = (int)$dataAsignacion->CantidadPremiosEliminados;
                                        $AsignacionPremiosEstadoLog->EstadoPremios = $dataAsignacion->EstadoPremios;
                                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_SUMAR;
                                        $AsignacionPremiosEstadoLog->Premios = (int)$value[1];
                                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $AsignacionPremiosEstadoLog->Motivo = $motivo;
                                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                                        $dataAsignacion->BNF3_Segmento_id = $id;
                                        $dataAsignacion->CantidadPremios =
                                            $dataAsignacion->CantidadPremios + (int)$value[1];
                                        $dataAsignacion->CantidadPremiosDisponibles =
                                            $dataAsignacion->CantidadPremiosDisponibles + (int)$value[1];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionPremiosTable()->saveAsignacion($dataAsignacion);
                                    } else {
                                        $asignacion = new AsignacionPremios();
                                        $asignacion->BNF3_Segmento_id = $id;
                                        $asignacion->BNF_Cliente_id = $dataCliente->id;
                                        $asignacion->CantidadPremios = (int)$value[1];
                                        $asignacion->CantidadPremiosDisponibles = (int)$value[1];
                                        $asignacion->EstadoPremios = 'Activado';
                                        $asignacion->Eliminado = 0;
                                        $asignacionId = $this->getAsignacionPremiosTable()->saveAsignacion($asignacion);

                                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $asignacionId;
                                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $asignacion->BNF3_Segmento_id;
                                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                        $AsignacionPremiosEstadoLog->CantidadPremios = $asignacion->CantidadPremios;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = 0;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles = $asignacion->CantidadPremiosDisponibles;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = 0;
                                        $AsignacionPremiosEstadoLog->EstadoPremios = $asignacion->EstadoPremios;
                                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                                        $AsignacionPremiosEstadoLog->Premios = $asignacion->CantidadPremios;
                                        $AsignacionPremiosEstadoLog->Motivo = "Creando Asignación";
                                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);
                                    }
                                }

                                if ((int)$value[2] > 0) {
                                    if ($dataAsignacion) {
                                        $AsignacionPremiosEstadoLog = new AsignacionPremiosEstadoLog();
                                        $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id = $dataAsignacion->id;
                                        $AsignacionPremiosEstadoLog->BNF3_Segmento_id = $dataAsignacion->BNF3_Segmento_id;
                                        $AsignacionPremiosEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $AsignacionPremiosEstadoLog->CantidadPremios =
                                            $dataAsignacion->CantidadPremios - (int)$value[2];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosUsados = (int)$dataAsignacion->CantidadPremiosUsados;
                                        $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles =
                                            $dataAsignacion->CantidadPremiosDisponibles - (int)$value[2];
                                        $AsignacionPremiosEstadoLog->CantidadPremiosEliminados = (int)$dataAsignacion->CantidadPremiosEliminados;
                                        $AsignacionPremiosEstadoLog->EstadoPremios = $dataAsignacion->EstadoPremios;
                                        $AsignacionPremiosEstadoLog->Operacion = $this::OPERACION_RESTAR;
                                        $AsignacionPremiosEstadoLog->Premios = (int)$value[2];
                                        $AsignacionPremiosEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $AsignacionPremiosEstadoLog->Motivo = "Restando Premios";
                                        $this->getAsignacionPremiosEstadoLogTable()->saveAsignacionPremiosEstadoLog($AsignacionPremiosEstadoLog);

                                        $dataAsignacion->BNF3_Segmento_id = $id;
                                        $dataAsignacion->BNF_Cliente_id = $dataCliente->id;
                                        $dataAsignacion->CantidadPremios =
                                            $dataAsignacion->CantidadPremios - (int)$value[2];
                                        $dataAsignacion->CantidadPremiosDisponibles =
                                            $dataAsignacion->CantidadPremiosDisponibles - (int)$value[2];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionPremiosTable()->saveAsignacion($dataAsignacion);
                                    }
                                }
                                $countValid++;
                            }

                            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
                            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
                            $pres = $this->getCampaniaTable()->getPresupuestoAcumulado($datoSegmento->BNF3_Campania_id);
                            $datoCampania->PresupuestoAsignado = $pres->PresupuestoAsignado;
                            $porcentaje = $this->getAsignacionPremiosTable()->getTotalAssignedByCampaign($datoCampania->id);
                            $total = $porcentaje->TotalAsignados;
                            if ($total >= $datoCampania->ParametroAlerta) {
                                $config = $this->getServiceLocator()->get('Config');
                                $email = $config['email_sender']['asignacion'];
                                $this->enviarCorreoAdmin($datoCampania, $total, $email);
                            }

                            $successMessage = 'Se registraron un total de ' . $countValid . ' Usuarios Finales.';

                            $dataAsignados = $this->getAsignacionPremiosTable()->getPersonalizedAssigned($id);

                            $datos = array();
                            $totalDatos = 0;
                            foreach ($dataAsignados as $data) {
                                $totalDatos++;
                                $datos[0][] = $data->BNF_Cliente_id;
                                $datos[1][] = (int)$data->CantidadPremios;
                                $datos[2][] = $data->EstadoPremios;
                                $totalAplicar = $totalAplicar + $data->CantidadPremios;
                            }

                            if (!empty($datos)) {
                                $numeroDocumento = $this->generarArreglosJS($datos[0]);
                                $premiosAsignados = $this->generarArreglosJS($datos[1]);
                                $estadoAsignacion = $this->generarArreglosJS($datos[2]);
                            }

                            $presupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo
                                - $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;
                        }
                        #endregion
                    }
                }
            }
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'asigpremios' => 'active',
                'id' => $id,
                'form' => $form,
                'confirm' => $confirm,
                'opcion' => $opcion,
                'type' => $type,
                'fileMessage' => $fileMessage,
                'errorMessageCsv' => $errorMessageCsv,
                'alertMessage' => $alertMessage,
                'successMessage' => $successMessage,
                'empresa' => $datoEmpresa->NombreComercial,
                'segmento' => $datoSegmento,
                'campania' => $datoCampania->NombreCampania,
                'presupuesto' => $presupuesto,
                'presupuestoNegociado' => $datoCampania->PresupuestoNegociado,
                'presupuestoAsignado' => $datoCampania->PresupuestoAsignado,
                'tipoSegmento' => $datoCampania->TipoSegmento,
                'totalDatos' => $totalDatos,
                'numeroDocumento' => $numeroDocumento,
                'estadoAsignacion' => $estadoAsignacion,
                'sumaPremios' => $sumaPremios,
                'restaPremios' => $restaPremios,
                'premiosAsignados' => $premiosAsignados,
                'numeroDocumentoMessage' => $numeroDocumentoMessage,
                'sumaPremiosMessage' => $sumaPremiosMessage,
                'restaPremiosMessage' => $restaPremiosMessage,
            )
        );
    }

    public function usuariosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('asignaciones-premios', array('action' => 'index'));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $documento = (trim($request->getPost()->documento) != "") ? trim($request->getPost()->documento) : "";
        } else {
            $documento = "";
        }

        try {
            $datoSegmento = $this->getSegmentosTable()->getSegmentosPremios($id);
            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
            $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
            $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
            $datoAsignacion = $this->getAsignacionPremiosTable()->getListaUsuariosAsignacion($id, $documento);
            $presupuesto = $datoSegmento->Subtotal;
            $tipoSegmento = $datoCampania->TipoSegmento == "Clasico" ? "Clásico" : "Personalizado";
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('asignaciones-premios', array('action' => 'index'));
        }

        return new ViewModel(
            array(
                'premios' => 'active',
                'asigpremios' => 'active',
                'id' => $id,
                'documento' => $documento,
                'asignaciones' => $datoAsignacion,
                'segmento' => $datoSegmento,
                'campania' => $datoCampania->NombreCampania,
                'empresa' => $datoEmpresa->NombreComercial,
                'tipoSegmento' => $tipoSegmento,
                'presupuesto' => $presupuesto,
            )
        );
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $empresa = (int)$this->params()->fromRoute('id', 0);
        $campania = (int)$this->params()->fromRoute('val', 0);

        $resultado = $this->getSegmentosTable()->getReporteAsignacion($empresa, $campania);
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Asignacion de Premios")
                ->setSubject("Asignacion de Premios")
                ->setDescription("Documento listado de Asignacion de Premios")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Asignacion de Premios");

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
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Empresa')
                ->setCellValue('C1', 'Campaña')
                ->setCellValue('D1', 'Segmentos')
                ->setCellValue('E1', 'Presupuestos Premios')
                ->setCellValue('F1', 'Premios Asignados')
                ->setCellValue('G1', 'Premios Disponibles');
            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Empresa)
                    ->setCellValue('C' . $i, $registro->Campania)
                    ->setCellValue('D' . $i, $registro->NombreSegmento)
                    ->setCellValue('E' . $i, $registro->Subtotal)
                    ->setCellValue('F' . $i, $registro->AsignadoActivo)
                    ->setCellValue('G' . $i, ($registro->Subtotal - $registro->AsignadoActivo));
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="AsignacionPremios.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
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
                        $campanias = $this->getCampaniaTable()->getCampaniasPByEmpresaActive($id);
                        $dataCampanias[] = array('id' => '0', 'text' => 'Seleccione...');
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

    public function enviarCorreo($dataSegmento, $email)
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $datoUsuarios = array();
        $datoSegmento = $this->getSegmentosTable()->getSegmentosPremios($dataSegmento->id);
        $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF3_Campania_id);
        $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
        $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
        $totalPremios = $this->getAsignacionPremiosTable()->getTotalAssigned($dataSegmento->id);
        $totalUsuarios = $this->getAsignacionPremiosTable()->getTotalUsers($dataSegmento->id);

        $totalPremiosDes = $this->getAsignacionPremiosTable()->getTotalAssignedDisabled($dataSegmento->id);
        $totalUsuariosDes = $this->getAsignacionPremiosTable()->getTotalUsersDisabled($dataSegmento->id);

        $plantilla = "";
        $presupuesto = 0;
        $asignado = ($totalUsuarios->TotalUsuarios * $totalPremios->TotalAsignados);

        $tipo = $datoCampania->TipoSegmento;
        if ($tipo == $this::TIPO_MESSAGE_CLASICO) {
            $plantilla = "mail-asignaciones-clasico";
            $presupuesto = ($datoSegmento->CantidadPremios * $datoSegmento->CantidadPersonas);
        } elseif ($tipo == $this::TIPO_MESSAGE_PERSONALIZADO) {
            $plantilla = "mail-asignaciones-personalizado";
            $presupuesto = $datoSegmento->Subtotal;
            $datoUsuarios = $this->getAsignacionPremiosTable()->getDetalleUsuariosDisabled($dataSegmento->id);
        }

        $mailContent = array(
            "campania" => $datoCampania->NombreCampania,
            "segmento" => $datoSegmento->NombreSegmento,
            "premios" => $datoSegmento->CantidadPremios,
            "personas" => $datoSegmento->CantidadPersonas,
            "presupuesto" => $presupuesto,
            "empresa" => $datoEmpresa->NombreComercial,
            "total_premios" => $totalPremios->TotalAsignados,
            "usuarios" => $totalUsuarios->TotalUsuarios,
            "total_premios_deshabilitados" => $totalPremiosDes->TotalAsignados,
            "usuarios_deshabilitados" => $totalUsuariosDes->TotalUsuarios,
            "lista_usuarios" => $datoUsuarios,
            "asignado" => $asignado,
        );

        $transport = $this->getServiceLocator()->get('mail.transport');
        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        $content = $renderer->render($plantilla, ['contenido' => $mailContent]);

        $messageEmail = new Message();
        $messageEmail->addTo($email)
            ->addFrom('premios@beneficios.pe', 'Beneficios.pe')
            ->setSubject('Desactivación de Segmentos');

        $htmlBody = new MimePart($content);
        $htmlBody->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlBody));
        $messageEmail->setBody($body);
        $transport->send($messageEmail);
    }

    public function enviarCorreoAdmin($campania, $total, $email)
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $total = number_format((float)$total, 2, '.', '');
        $mailContent = array(
            "nombre" => $campania->NombreCampania,
            "tipo" => $campania->TipoSegmento,
            "presupuesto" => $campania->PresupuestoNegociado,
            "total" => $total,
        );

        $transport = $this->getServiceLocator()->get('mail.transport');
        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        $content = $renderer->render('mail-asignaciones-premios-admin', ['contenido' => $mailContent]);

        $messageEmail = new Message();
        $messageEmail->addTo($email)
            ->addFrom('asignacion@beneficios.pe', 'Beneficios.pe')
            ->setSubject('Asignacion de premios');

        $htmlBody = new MimePart($content);
        $htmlBody->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlBody));
        $messageEmail->setBody($body);
        $transport->send($messageEmail);
    }

    public function validarCampos($request)
    {
        $approved = false;
        $messages = array();
        $validAlnum = new Regex(array('pattern' => "/^([a-zA-Z0-9\/\-])+$/"));
        $validDigits = new IsInt();
        $validNotEmpty = new NotEmpty(NotEmpty::ALL);

        $numeroDocumento = $request->getPost()->numeroDocumento;
        $sumaPremios = $request->getPost()->sumaPremios;
        $restaPremios = $request->getPost()->restaPremios;

        if (count($numeroDocumento) == count($sumaPremios) and count($sumaPremios) == count($restaPremios)) {
            //Validar Documentos
            $numeroDocumentoState = true;
            if (is_array($numeroDocumento) || is_object($numeroDocumento)) {
                foreach ($numeroDocumento as $value) {
                    $value = trim($value);
                    if (!$validNotEmpty($value)) {
                        $messages['numeroDocumento'][] = "El campo no puede quedar vacío.";
                        $numeroDocumentoState = false;
                    } elseif (!$validAlnum($value)) {
                        $messages['numeroDocumento'][] = "El valor ingresado: " . $value . ", no es válido.";
                        $numeroDocumentoState = false;
                    } else {
                        $messages['numeroDocumento'][] = "";
                    }
                }
            } else {
                $numeroDocumentoState = false;
            }

            //Validar Cantidad de Premios a Sumar
            $sumaPremiosState = true;
            if (is_array($sumaPremios) || is_object($sumaPremios)) {
                foreach ($sumaPremios as $value) {
                    $value = trim($value);
                    if (!$validDigits($value) && $value != "") {
                        $messages['sumaPremios'][] = "El campo solo acepta números enteros.";
                        $sumaPremiosState = false;
                    } else {
                        $messages['sumaPremios'][] = "";
                    }
                }
            } else {
                $sumaPremiosState = false;
            }

            //Validar Cantidad de Premios a Restar
            $restaPremiosState = true;
            if (is_array($restaPremios) || is_object($restaPremios)) {
                foreach ($restaPremios as $value) {
                    $value = trim($value);
                    if (!$validDigits($value) && $value != "") {
                        $messages['restaPremios'][] = "El campo solo acepta números enteros.";
                        $restaPremiosState = false;
                    } else {
                        $messages['restaPremios'][] = "";
                    }
                }
            } else {
                $restaPremiosState = false;
            }

            //Comprobando validaciones
            if ($numeroDocumentoState and $sumaPremiosState and $restaPremiosState) {
                $approved = true;
            }
        }
        return array($approved, $messages);
    }

    public function generarArreglosJS($arreglo)
    {
        $temp = "[";
        $contador = 0;
        if (is_array($arreglo)) {
            foreach ($arreglo as $value) {
                $temp = $contador > 0 ? $temp . ", '" . addslashes($value) . "'" : $temp . "'" . addslashes($value) . "'";
                $contador++;
            }
        }
        $temp = $temp . "]";
        $arreglo = $temp;
        return $arreglo;
    }
}
