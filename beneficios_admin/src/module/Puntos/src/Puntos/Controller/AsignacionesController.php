<?php

namespace Puntos\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Puntos\Form\BuscarAsignacionPuntos;
use Puntos\Form\FormAsignarPuntos;
use Puntos\Model\Asignacion;
use Puntos\Model\AsignacionEstadoLog;
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
    const MESSAGE_ERROR_PRESUPUESTO = "La cantidad de puntos ingresada es mayor al presupuesto del Segmento";
    const MESSAGE_SUCCESS = "Puntos Asignados Correctamente";

    const TIPO_MESSAGE_CLASICO = "Clasico";
    const TIPO_MESSAGE_PERSONALIZADO = "Personalizado";

    const ESTADO_PUNTOS_ACTIVO = "Activado";
    const ESTADO_PUNTOS_DESACTIVADO = "Desactivado";

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
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPTable');
    }

    public function getSegmentosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\SegmentosPTable');
    }

    public function getCampaniaEmpresaTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPEmpresasTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\AsignacionTable');
    }

    public function getEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaClienteClienteTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\ClienteTable');
    }

    public function getAsignacionEstadoLogTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\AsignacionEstadoLogTable');
    }
    #endregion

    #region Inicializando Data
    public function inicializacion()
    {
        $dataEmpresa = array();
        $filterEmpresa = array();

        try {
            foreach ($this->getCampaniaTable()->getEmpresasCliente("busqueda") as $empresa) {
                $dataEmpresa[$empresa->id] = $empresa->Empresa;
                $filterEmpresa[$empresa->id] = [$empresa->id];
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
            //$form = new BuscarAsignacionPuntos('buscar', $empresa_value, $tipo_usuario);
            $form = new BuscarAsignacionPuntos('buscar', $data[0], $tipo_usuario);
        } else {
            $form = new BuscarAsignacionPuntos('buscar asignaciones', $data[0]);
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
            $order_by = 'BNF2_Campanias.FechaCreacion';
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
                'puntos' => 'active',
                'asigptos' => 'active',
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
            return $this->redirect()->toRoute('asignaciones-puntos', array('action' => 'index'));
        }

        try {
            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
            $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
            $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);

            $totalAsignado = $this->getAsignacionTable()->getTotalAssigned($id)->TotalAsignados;
            $totalUsuariosAsignados = $this->getAsignacionTable()->getTotalUsers($id)->TotalUsuarios;

            $tipoSegmento = $datoCampania->TipoSegmento == "Clasico" ? "Clásico" : "Clasico";
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('asignaciones-puntos', array('action' => 'index'));
        }

        $opcion = ((int)$datoSegmento->Eliminado) ? false : true;

        $form = new FormAsignarPuntos();
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
                                                    $dataAsignado = $this->getAsignacionTable()
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
                                                    . " no está registrado, ningún punto fue asignado";
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
                    $puntos = $datoSegmento->CantidadPuntos;

                    foreach ($dataCsv as $value) {
                        $dataCliente = $this->getClienteTable()->getClientByDoc($value);

                        $asignacion = new Asignacion();
                        $asignacion->BNF2_Segmento_id = $datoSegmento->id;
                        $asignacion->BNF_Cliente_id = $dataCliente->id;
                        $asignacion->TipoAsignamiento = 'Normal';
                        $asignacion->CantidadPuntos = $puntos;
                        $asignacion->CantidadPuntosDisponibles = $puntos;
                        $asignacion->EstadoPuntos = 'Activado';
                        $asignacion->Eliminado = 0;

                        $asignacionId = $this->getAsignacionTable()->saveAsignacion($asignacion);

                        $asignacionEstadoLog = new AsignacionEstadoLog();
                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacionId;
                        $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                        $asignacionEstadoLog->CantidadPuntos = $asignacion->CantidadPuntos;
                        $asignacionEstadoLog->CantidadPuntosUsados = 0;
                        $asignacionEstadoLog->CantidadPuntosDisponibles = $asignacion->CantidadPuntosDisponibles;
                        $asignacionEstadoLog->CantidadPuntosEliminados = 0;
                        $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                        $asignacionEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                        $asignacionEstadoLog->Puntos = $asignacion->CantidadPuntos;
                        $asignacionEstadoLog->Motivo = "Creando Asignación";
                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                        $countValid++;

                        $totalPresupuesto = $totalPresupuesto - $datoSegmento->CantidadPuntos;
                        if ($totalPresupuesto >= $datoSegmento->CantidadPuntos) {
                            $puntos = $datoSegmento->CantidadPuntos;
                        } else {
                            $puntos = $totalPresupuesto;
                        }
                    }

                    $datoSegmento = $this->getSegmentosTable()->getSegmentosP($id);
                    $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
                    $porcentaje = $this->getAsignacionTable()->getTotalAssignedByCampaign($datoCampania->id);
                    $total = $porcentaje->TotalAsignados;
                    if ($total >= $datoCampania->ParametroAlerta && !empty($datoCampania->ParametroAlerta)) {
                        $config = $this->getServiceLocator()->get('Config');
                        $email = $config['email_sender']['asignacion'];
                        $this->enviarCorreoAdmin($datoCampania, $total, $email);
                    }

                    $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
                    $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
                    $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
                    $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
                    $totalAsignado = $this->getAsignacionTable()->getTotalAssigned($id)->TotalAsignados;
                    $totalUsuariosAsignados = $this->getAsignacionTable()->getTotalUsers($id)->TotalUsuarios;
                    $successMessage = 'Se registraron un total de ' . $countValid . ' Usuarios Finales.';
                }
            }
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'asigptos' => 'active',
                'id' => $id,
                'form' => $form,
                'opcion' => $opcion,
                'segmento' => $datoSegmento,
                'campania' => $datoCampania->NombreCampania,
                'estado_campania' => $datoCampania->EstadoCampania,
                'tipoSegmento' => $tipoSegmento,
                'empresa' => $datoEmpresa->NombreComercial,
                'totalAsignado' => $totalAsignado,
                'totalPuntos' => $totalAsignado,
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
        $sumaPuntos = "";
        $restaPuntos = "";
        $puntosAsignados = "";
        $estadoAsignacion = "";
        $numeroDocumentoMessage = "";
        $sumaPuntosMessage = "";
        $restaPuntosMessage = "";

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
            return $this->redirect()->toRoute('asignaciones-puntos', array('action' => 'index'));
        }

        try {
            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
            $pres = $this->getCampaniaTable()->getPresupuestoAcumulado($datoSegmento->BNF2_Campania_id);
            $datoCampania->PresupuestoAsignado = $pres->PresupuestoAsignado;
            $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
            $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
            $dataAsignados = $this->getAsignacionTable()->getPersonalizedAssigned($id);

            $datos = array();
            foreach ($dataAsignados as $data) {
                $totalDatos++;
                $datos[0][] = $data->BNF_Cliente_id;
                $datos[1][] = (int)$data->CantidadPuntos;
                $datos[2][] = $data->EstadoPuntos;
                $totalAplicar = $totalAplicar + $data->CantidadPuntos;
            }

            if (!empty($datos)) {
                $numeroDocumento = $this->generarArreglosJS($datos[0]);
                $puntosAsignados = $this->generarArreglosJS($datos[1]);
                $estadoAsignacion = $this->generarArreglosJS($datos[2]);
            }

            $presupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo
                - $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('asignaciones-puntos', array('action' => 'index'));
        }

        $opcion = ((int)$datoSegmento->Eliminado) ? false : true;

        $form = new FormAsignarPuntos();
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
                    $dataSumaPuntos = $request->getPost()->sumaPuntos;
                    $dataRestaPuntos = $request->getPost()->restaPuntos;
                    $dataEstadoPuntos = $request->getPost()->estado;
                    $totalDatos = count($dataDocumentos);

                    //Validar total de punto
                    $totalPuntos = 0;
                    foreach ($dataSumaPuntos as $punto) {
                        $totalPuntos = $totalPuntos + $punto;
                    }

                    if ($totalPuntos <= $presupuesto) {
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
                                        if ($this->getAsignacionTable()
                                            ->getPersonalizedAssignedDisabled($datoSegmento->id, $documento)
                                        ) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $documento . " tiene una asignación desactivada";
                                            $type = "danger";
                                        }
                                    }
                                } else {
                                    $documentoEstado = false;
                                    $confirm[] = "El documento " . $documento . " no está registrado, ningún punto fue asignado";
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

                        #region Validar Puntos
                        if ($documentoEstado) {
                            foreach ($dataDocumentos as $key => $documento) {
                                $dataCliente = $this->getClienteTable()->getClientByDoc($documento);
                                $dataAsignacion = $this->getAsignacionTable()
                                    ->getAsignacionCliente($id, $dataCliente->id);

                                if ($dataAsignacion) {
                                    if ((int)$dataAsignacion->Eliminado == 0) {
                                        $resultado = $dataAsignacion->CantidadPuntos + (int)$dataSumaPuntos[$key] - (int)$dataRestaPuntos[$key];
                                        $puntosUsados = $dataAsignacion->CantidadPuntosUsados;

                                        if ($resultado < $puntosUsados) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $documento . " no se le puede restar una cantidad " .
                                                "mayor a los puntos usados";
                                            $type = "danger";
                                        } else {
                                            if ($resultado < 0) {
                                                $documentoEstado = false;
                                                $confirm[] = "El documento " . $documento . " no se le puede restar una " .
                                                    "cantidad mayor a sus puntos asignados";
                                                $type = "danger";
                                            }
                                        }
                                    } else {
                                        if ((int)$dataRestaPuntos[$key] > 0) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $documento .
                                                " esta cancelado y no se puede restar puntos";
                                            $type = "danger";
                                        }
                                    }
                                } else {
                                    if ((int)$dataSumaPuntos[$key] == 0 && (int)$dataRestaPuntos[$key] == 0) {
                                        $documentoEstado = false;
                                        $confirm[] = "No se ingresó una cantidad de puntos para el documento " . $documento;
                                        $type = "danger";
                                    } elseif ((int)$dataSumaPuntos[$key] == 0 && (int)$dataRestaPuntos[$key] > 0) {
                                        $documentoEstado = false;
                                        $confirm[] = "El documento " . $documento . " no tiene ningún punto asignado para restar";
                                        $type = "danger";
                                    }
                                }
                            }
                        }
                        #endregion

                        #region Asignacion de Puntos
                        if ($documentoEstado) {
                            foreach ($dataDocumentos as $key => $value) {
                                $dataCliente = $this->getClienteTable()->getClientByDoc($value);
                                $dataAsignacion = $this->getAsignacionTable()
                                    ->getAsignacionCliente($id, $dataCliente->id);

                                if ((int)$dataSumaPuntos[$key] > 0) {
                                    if ($dataAsignacion) {
                                        if ($dataAsignacion->EstadoPuntos == "Cancelado") {
                                            $dataAsignacion->EstadoPuntos = "Activado";
                                            $motivo = "Agregando Puntos después de Cancelación";
                                        } else {
                                            $motivo = "Agregando Puntos";
                                        }

                                        $asignacionEstadoLog = new AsignacionEstadoLog();
                                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $dataAsignacion->id;
                                        $asignacionEstadoLog->BNF2_Segmento_id = $dataAsignacion->BNF2_Segmento_id;
                                        $asignacionEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                                        $asignacionEstadoLog->CantidadPuntos = $dataAsignacion->CantidadPuntos + (int)$dataSumaPuntos[$key];
                                        $asignacionEstadoLog->CantidadPuntosUsados = (int)$dataAsignacion->CantidadPuntosUsados;
                                        $asignacionEstadoLog->CantidadPuntosDisponibles = $dataAsignacion->CantidadPuntosDisponibles + (int)$dataSumaPuntos[$key];
                                        $asignacionEstadoLog->CantidadPuntosEliminados = (int)$dataAsignacion->CantidadPuntosEliminados;
                                        $asignacionEstadoLog->EstadoPuntos = $dataAsignacion->EstadoPuntos;
                                        $asignacionEstadoLog->Operacion = $this::OPERACION_SUMAR;
                                        $asignacionEstadoLog->Puntos = (int)$dataSumaPuntos[$key];
                                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $asignacionEstadoLog->Motivo = $motivo;
                                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                                        $dataAsignacion->BNF2_Segmento_id = $id;
                                        $dataAsignacion->CantidadPuntos =
                                            $dataAsignacion->CantidadPuntos + (int)$dataSumaPuntos[$key];
                                        $dataAsignacion->CantidadPuntosDisponibles =
                                            $dataAsignacion->CantidadPuntosDisponibles + (int)$dataSumaPuntos[$key];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionTable()->saveAsignacion($dataAsignacion);
                                    } else {
                                        $asignacion = new Asignacion();
                                        $asignacion->BNF2_Segmento_id = $id;
                                        $asignacion->BNF_Cliente_id = $dataCliente->id;
                                        $asignacion->TipoAsignamiento = 'Normal';
                                        $asignacion->CantidadPuntos = (int)$dataSumaPuntos[$key];
                                        $asignacion->CantidadPuntosDisponibles = (int)$dataSumaPuntos[$key];
                                        $asignacion->EstadoPuntos = 'Activado';
                                        $asignacion->Eliminado = 0;
                                        $asignacionId = $this->getAsignacionTable()->saveAsignacion($asignacion);

                                        $asignacionEstadoLog = new AsignacionEstadoLog();
                                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacionId;
                                        $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                                        $asignacionEstadoLog->CantidadPuntos = $asignacion->CantidadPuntos;
                                        $asignacionEstadoLog->CantidadPuntosUsados = 0;
                                        $asignacionEstadoLog->CantidadPuntosDisponibles = $asignacion->CantidadPuntosDisponibles;
                                        $asignacionEstadoLog->CantidadPuntosEliminados = 0;
                                        $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                                        $asignacionEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                                        $asignacionEstadoLog->Puntos = $asignacion->CantidadPuntos;
                                        $asignacionEstadoLog->Motivo = "Creando Asignación";
                                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);
                                    }
                                }

                                if ((int)$dataRestaPuntos[$key] > 0) {
                                    if ($dataAsignacion) {
                                        $asignacionEstadoLog = new AsignacionEstadoLog();
                                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $dataAsignacion->id;
                                        $asignacionEstadoLog->BNF2_Segmento_id = $dataAsignacion->BNF2_Segmento_id;
                                        $asignacionEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                                        $asignacionEstadoLog->CantidadPuntos =
                                            $dataAsignacion->CantidadPuntos - (int)$dataRestaPuntos[$key];
                                        $asignacionEstadoLog->CantidadPuntosUsados = (int)$dataAsignacion->CantidadPuntosUsados;
                                        $asignacionEstadoLog->CantidadPuntosDisponibles =
                                            $dataAsignacion->CantidadPuntosDisponibles - (int)$dataRestaPuntos[$key];
                                        $asignacionEstadoLog->CantidadPuntosEliminados = (int)$dataAsignacion->CantidadPuntosEliminados;
                                        $asignacionEstadoLog->EstadoPuntos = $dataAsignacion->EstadoPuntos;
                                        $asignacionEstadoLog->Operacion = $this::OPERACION_RESTAR;
                                        $asignacionEstadoLog->Puntos = (int)$dataRestaPuntos[$key];
                                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $asignacionEstadoLog->Motivo = "Restando Puntos";
                                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                                        $dataAsignacion->BNF2_Segmento_id = $id;
                                        $dataAsignacion->BNF_Cliente_id = $dataCliente->id;
                                        $dataAsignacion->CantidadPuntos =
                                            $dataAsignacion->CantidadPuntos - (int)$dataRestaPuntos[$key];
                                        $dataAsignacion->CantidadPuntosDisponibles =
                                            $dataAsignacion->CantidadPuntosDisponibles - (int)$dataRestaPuntos[$key];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionTable()->saveAsignacion($dataAsignacion);
                                    }
                                }
                            }

                            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
                            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
                            $pres = $this->getCampaniaTable()->getPresupuestoAcumulado($datoSegmento->BNF2_Campania_id);
                            $datoCampania->PresupuestoAsignado = $pres->PresupuestoAsignado;
                            $porcentaje = $this->getAsignacionTable()->getTotalAssignedByCampaign($datoCampania->id);
                            $total = $porcentaje->TotalAsignados;

                            if ($total >= $datoCampania->ParametroAlerta && !empty($datoCampania->ParametroAlerta)) {
                                $config = $this->getServiceLocator()->get('Config');
                                $email = $config['email_sender']['asignacion'];
                                $this->enviarCorreoAdmin($datoCampania, $total, $email);
                            }

                            $sumaPuntos = "";
                            $restaPuntos = "";
                            $confirm[] = $this::MESSAGE_SUCCESS;
                            $type = "success";

                            $dataAsignados = $this->getAsignacionTable()->getPersonalizedAssigned($id);

                            $datos = array();
                            $totalDatos = 0;
                            foreach ($dataAsignados as $data) {
                                $totalDatos++;
                                $datos[0][] = $data->BNF_Cliente_id;
                                $datos[1][] = (int)$data->CantidadPuntos;
                                $datos[2][] = $data->EstadoPuntos;
                                $totalAplicar = $totalAplicar + $data->CantidadPuntos;
                            }

                            if (!empty($datos)) {
                                $numeroDocumento = $this->generarArreglosJS($datos[0]);
                                $puntosAsignados = $this->generarArreglosJS($datos[1]);
                                $estadoAsignacion = $this->generarArreglosJS($datos[2]);
                            }

                            $presupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo
                                - $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;
                        } else {
                            //Datos
                            $numeroDocumento = $this->generarArreglosJS($request->getPost()->numeroDocumento);
                            $sumaPuntos = $this->generarArreglosJS($request->getPost()->sumaPuntos);
                            $restaPuntos = $this->generarArreglosJS($request->getPost()->restaPuntos);
                            $estadoAsignacion = $this->generarArreglosJS($request->getPost()->estado);
                            //Mensajes de Error
                            $numeroDocumentoMessage = $this->generarArreglosJS($message["numeroDocumento"]);
                            $sumaPuntosMessage = $this->generarArreglosJS($message["sumaPuntos"]);
                            $restaPuntosMessage = $this->generarArreglosJS($message["restaPuntos"]);
                        }
                        #endregion
                    } else {
                        //Datos
                        $numeroDocumento = $this->generarArreglosJS($request->getPost()->numeroDocumento);
                        $sumaPuntos = $this->generarArreglosJS($request->getPost()->sumaPuntos);
                        $restaPuntos = $this->generarArreglosJS($request->getPost()->restaPuntos);
                        $estadoAsignacion = $this->generarArreglosJS($request->getPost()->estado);
                        //Mensajes de Error
                        $numeroDocumentoMessage = $this->generarArreglosJS($message["numeroDocumento"]);
                        $sumaPuntosMessage = $this->generarArreglosJS($message["sumaPuntos"]);
                        $restaPuntosMessage = $this->generarArreglosJS($message["restaPuntos"]);
                        $confirm[] = $this::MESSAGE_ERROR_PRESUPUESTO;
                        $type = "danger";
                    }
                } else {
                    //Datos
                    $numeroDocumento = $this->generarArreglosJS($request->getPost()->numeroDocumento);
                    $sumaPuntos = $this->generarArreglosJS($request->getPost()->sumaPuntos);
                    $restaPuntos = $this->generarArreglosJS($request->getPost()->restaPuntos);
                    $estadoAsignacion = $this->generarArreglosJS($request->getPost()->estado);
                    //Mensajes de Error
                    $numeroDocumentoMessage = $this->generarArreglosJS(@$message["numeroDocumento"]);
                    $sumaPuntosMessage = $this->generarArreglosJS(@$message["sumaPuntos"]);
                    $restaPuntosMessage = $this->generarArreglosJS(@$message["restaPuntos"]);
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
                                                if ($this->getAsignacionTable()
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
                                                . " no está registrado, ningún punto fue asignado";
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
                                            'el total de puntos supera al presupuesto.';
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
                        #region Validar Puntos
                        foreach ($dataCsv as $value) {
                            $dataCliente = $this->getClienteTable()->getClientByDoc($value[0]);
                            $dataAsignacion = $this->getAsignacionTable()
                                ->getAsignacionCliente($id, $dataCliente->id);

                            if ($dataAsignacion) {
                                if ((int)$dataAsignacion->Eliminado == 0) {
                                    $resultado = $dataAsignacion->CantidadPuntos + (int)$value[1] - (int)$value[2];
                                    $puntosUsados = $dataAsignacion->CantidadPuntosUsados;
                                    if ($resultado < $puntosUsados) {
                                        $documentoEstado = false;
                                        $confirm[] = "El documento " . $value[0] .
                                            " no se le puede restar una cantidad mayor a los puntos usados";
                                        $type = "danger";
                                    } else {
                                        if ($resultado < 0) {
                                            $documentoEstado = false;
                                            $confirm[] = "El documento " . $value[0] . " no se le puede restar una " .
                                                "cantidad mayor a sus puntos asignados";
                                            $type = "danger";
                                        }
                                    }
                                } else {
                                    if ((int)$value[2] > 0) {
                                        $documentoEstado = false;
                                        $confirm[] = "El documento " . $value[0] .
                                            " esta cancelado y no se puede restar puntos";
                                        $type = "danger";
                                    }
                                }
                            } else {
                                if ((int)$value[2] > 0) {
                                    $documentoEstado = false;
                                    $confirm[] = "El documento " . $value[0] . " no tiene ningún punto asignado";
                                    $type = "danger";
                                }
                            }
                        }
                        #endregion

                        #region Asignacion de Puntos
                        if ($documentoEstado) {
                            foreach ($dataCsv as $value) {
                                $dataCliente = $this->getClienteTable()->getClientByDoc($value[0]);
                                $dataAsignacion = $this->getAsignacionTable()
                                    ->getAsignacionCliente($id, $dataCliente->id);

                                if ((int)$value[1] > 0) {
                                    if ($dataAsignacion) {
                                        if ($dataAsignacion->EstadoPuntos == "Cancelado") {
                                            $dataAsignacion->EstadoPuntos = "Activado";
                                            $motivo = "Agregando Puntos después de Cancelación";
                                        } else {
                                            $motivo = "Agregando Puntos";
                                        }

                                        $asignacionEstadoLog = new AsignacionEstadoLog();
                                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $dataAsignacion->id;
                                        $asignacionEstadoLog->BNF2_Segmento_id = $dataAsignacion->BNF2_Segmento_id;
                                        $asignacionEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $asignacionEstadoLog->CantidadPuntos = $dataAsignacion->CantidadPuntos + (int)$value[1];
                                        $asignacionEstadoLog->CantidadPuntosUsados = (int)$dataAsignacion->CantidadPuntosUsados;
                                        $asignacionEstadoLog->CantidadPuntosDisponibles = $dataAsignacion->CantidadPuntosDisponibles + (int)$value[1];
                                        $asignacionEstadoLog->CantidadPuntosEliminados = (int)$dataAsignacion->CantidadPuntosEliminados;
                                        $asignacionEstadoLog->EstadoPuntos = $dataAsignacion->EstadoPuntos;
                                        $asignacionEstadoLog->Operacion = $this::OPERACION_SUMAR;
                                        $asignacionEstadoLog->Puntos = (int)$value[1];
                                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $asignacionEstadoLog->Motivo = $motivo;
                                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                                        $dataAsignacion->BNF2_Segmento_id = $id;
                                        $dataAsignacion->CantidadPuntos =
                                            $dataAsignacion->CantidadPuntos + (int)$value[1];
                                        $dataAsignacion->CantidadPuntosDisponibles =
                                            $dataAsignacion->CantidadPuntosDisponibles + (int)$value[1];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionTable()->saveAsignacion($dataAsignacion);
                                    } else {
                                        $asignacion = new Asignacion();
                                        $asignacion->BNF2_Segmento_id = $id;
                                        $asignacion->BNF_Cliente_id = $dataCliente->id;
                                        $dataAsignacion->TipoAsignamiento = 'Normal';
                                        $asignacion->CantidadPuntos = (int)$value[1];
                                        $asignacion->CantidadPuntosDisponibles = (int)$value[1];
                                        $asignacion->EstadoPuntos = 'Activado';
                                        $asignacion->Eliminado = 0;
                                        $asignacionId = $this->getAsignacionTable()->saveAsignacion($asignacion);

                                        $asignacionEstadoLog = new AsignacionEstadoLog();
                                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacionId;
                                        $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                                        $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                                        $asignacionEstadoLog->CantidadPuntos = $asignacion->CantidadPuntos;
                                        $asignacionEstadoLog->CantidadPuntosUsados = 0;
                                        $asignacionEstadoLog->CantidadPuntosDisponibles = $asignacion->CantidadPuntosDisponibles;
                                        $asignacionEstadoLog->CantidadPuntosEliminados = 0;
                                        $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                                        $asignacionEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                                        $asignacionEstadoLog->Puntos = $asignacion->CantidadPuntos;
                                        $asignacionEstadoLog->Motivo = "Creando Asignación";
                                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);
                                    }
                                }

                                if ((int)$value[2] > 0) {
                                    if ($dataAsignacion) {
                                        $asignacionEstadoLog = new AsignacionEstadoLog();
                                        $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $dataAsignacion->id;
                                        $asignacionEstadoLog->BNF2_Segmento_id = $dataAsignacion->BNF2_Segmento_id;
                                        $asignacionEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                        $asignacionEstadoLog->TipoAsignamiento = 'Normal';
                                        $asignacionEstadoLog->CantidadPuntos =
                                            $dataAsignacion->CantidadPuntos - (int)$value[2];
                                        $asignacionEstadoLog->CantidadPuntosUsados = (int)$dataAsignacion->CantidadPuntosUsados;
                                        $asignacionEstadoLog->CantidadPuntosDisponibles =
                                            $dataAsignacion->CantidadPuntosDisponibles - (int)$value[2];
                                        $asignacionEstadoLog->CantidadPuntosEliminados = (int)$dataAsignacion->CantidadPuntosEliminados;
                                        $asignacionEstadoLog->EstadoPuntos = $dataAsignacion->EstadoPuntos;
                                        $asignacionEstadoLog->Operacion = $this::OPERACION_RESTAR;
                                        $asignacionEstadoLog->Puntos = (int)$value[2];
                                        $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                        $asignacionEstadoLog->Motivo = "Restando Puntos";
                                        $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                                        $dataAsignacion->BNF2_Segmento_id = $id;
                                        $dataAsignacion->BNF_Cliente_id = $dataCliente->id;
                                        $dataAsignacion->CantidadPuntos =
                                            $dataAsignacion->CantidadPuntos - (int)$value[2];
                                        $dataAsignacion->CantidadPuntosDisponibles =
                                            $dataAsignacion->CantidadPuntosDisponibles - (int)$value[2];
                                        $dataAsignacion->Eliminado = 0;
                                        $this->getAsignacionTable()->saveAsignacion($dataAsignacion);
                                    }
                                }
                                $countValid++;
                            }

                            $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($id);
                            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
                            $pres = $this->getCampaniaTable()->getPresupuestoAcumulado($datoSegmento->BNF2_Campania_id);
                            $datoCampania->PresupuestoAsignado = $pres->PresupuestoAsignado;
                            $porcentaje = $this->getAsignacionTable()->getTotalAssignedByCampaign($datoCampania->id);
                            $total = $porcentaje->TotalAsignados;
                            if ($total >= $datoCampania->ParametroAlerta) {
                                $config = $this->getServiceLocator()->get('Config');
                                $email = $config['email_sender']['asignacion'];
                                $this->enviarCorreoAdmin($datoCampania, $total, $email);
                            }

                            $successMessage = 'Se registraron un total de ' . $countValid . ' Usuarios Finales.';

                            $dataAsignados = $this->getAsignacionTable()->getPersonalizedAssigned($id);

                            $datos = array();
                            $totalDatos = 0;
                            foreach ($dataAsignados as $data) {
                                $totalDatos++;
                                $datos[0][] = $data->BNF_Cliente_id;
                                $datos[1][] = (int)$data->CantidadPuntos;
                                $datos[2][] = $data->EstadoPuntos;
                                $totalAplicar = $totalAplicar + $data->CantidadPuntos;
                            }

                            if (!empty($datos)) {
                                $numeroDocumento = $this->generarArreglosJS($datos[0]);
                                $puntosAsignados = $this->generarArreglosJS($datos[1]);
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
                'puntos' => 'active',
                'asigptos' => 'active',
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
                'sumaPuntos' => $sumaPuntos,
                'restaPuntos' => $restaPuntos,
                'puntosAsignados' => $puntosAsignados,
                'numeroDocumentoMessage' => $numeroDocumentoMessage,
                'sumaPuntosMessage' => $sumaPuntosMessage,
                'restaPuntosMessage' => $restaPuntosMessage,
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
            return $this->redirect()->toRoute('asignaciones-puntos', array('action' => 'index'));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $documento = (trim($request->getPost()->documento) != "") ? trim($request->getPost()->documento) : "";
        } else {
            $documento = "";
        }

        try {
            $datoSegmento = $this->getSegmentosTable()->getSegmentosP($id);
            $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
            $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
            $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
            $datoAsignacion = $this->getAsignacionTable()->getListaUsuariosAsignacion($id, $documento);
            $presupuesto = $datoSegmento->Subtotal;
            $tipoSegmento = $datoCampania->TipoSegmento == "Clasico" ? "Clásico" : "Personalizado";
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('asignaciones-puntos', array('action' => 'index'));
        }

        return new ViewModel(
            array(
                'puntos' => 'active',
                'asigptos' => 'active',
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
                ->setTitle("Asignacion de Puntos")
                ->setSubject("Asignacion de Puntos")
                ->setDescription("Documento listado de Asignacion de Puntos")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Asignacion de Puntos");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:I' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

            #region Styles
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
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
            #endregion

            $objPHPExcel->getActiveSheet()->getStyle('A1:I' . ($registros + 1))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Empresa Cliente')
                ->setCellValue('C1', 'Campaña')
                ->setCellValue('D1', 'Segmento')
                ->setCellValue('E1', 'Presupuesto Puntos')
                ->setCellValue('F1', 'Puntos Asignados Activos')
                ->setCellValue('G1', 'Puntos Disponibles')
                ->setCellValue('H1', 'Estado de la campaña')
                ->setCellValue('I1', 'Tipo');
            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Empresa)
                    ->setCellValue('C' . $i, $registro->Campania)
                    ->setCellValue('D' . $i, $registro->NombreSegmento)
                    ->setCellValue('E' . $i, $registro->Subtotal)
                    ->setCellValue('F' . $i, ($registro->Eliminado == 0) ? $registro->AsignadoActivo : ' - ')
                    ->setCellValue('G' . $i, ($registro->Eliminado == 0) ? $registro->DisponibleAsignar : ' - ')
                    ->setCellValue('H' . $i, $registro->EstadoCampania)
                    ->setCellValue('I' . $i, $registro->Tipo == "Clasico" ? "Clásico" : "Personalizado");
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="AsignacionPuntos.xlsx"');
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
        $datoSegmento = $this->getSegmentosTable()->getSegmentosP($dataSegmento->id);
        $datoCampania = $this->getCampaniaTable()->getCampaniasP($datoSegmento->BNF2_Campania_id);
        $datoCampaniaEmpresa = $this->getCampaniaEmpresaTable()->getbyCampaniasP($datoCampania->id);
        $datoEmpresa = $this->getEmpresaTable()->getEmpresa($datoCampaniaEmpresa->BNF_Empresa_id);
        $totalPuntos = $this->getAsignacionTable()->getTotalAssigned($dataSegmento->id);
        $totalUsuarios = $this->getAsignacionTable()->getTotalUsers($dataSegmento->id);

        $totalPuntosDes = $this->getAsignacionTable()->getTotalAssignedDisabled($dataSegmento->id);
        $totalUsuariosDes = $this->getAsignacionTable()->getTotalUsersDisabled($dataSegmento->id);

        $plantilla = "";
        $presupuesto = 0;
        $asignado = ($totalUsuarios->TotalUsuarios * $totalPuntos->TotalAsignados);

        $tipo = $datoCampania->TipoSegmento;
        if ($tipo == $this::TIPO_MESSAGE_CLASICO) {
            $plantilla = "mail-asignaciones-clasico";
            $presupuesto = ($datoSegmento->CantidadPuntos * $datoSegmento->CantidadPersonas);
        } elseif ($tipo == $this::TIPO_MESSAGE_PERSONALIZADO) {
            $plantilla = "mail-asignaciones-personalizado";
            $presupuesto = $datoSegmento->Subtotal;
            $datoUsuarios = $this->getAsignacionTable()->getDetalleUsuariosDisabled($dataSegmento->id);
        }

        $mailContent = array(
            "campania" => $datoCampania->NombreCampania,
            "segmento" => $datoSegmento->NombreSegmento,
            "puntos" => $datoSegmento->CantidadPuntos,
            "personas" => $datoSegmento->CantidadPersonas,
            "presupuesto" => $presupuesto,
            "empresa" => $datoEmpresa->NombreComercial,
            "total_puntos" => $totalPuntos->TotalAsignados,
            "usuarios" => $totalUsuarios->TotalUsuarios,
            "total_puntos_deshabilitados" => $totalPuntosDes->TotalAsignados,
            "usuarios_deshabilitados" => $totalUsuariosDes->TotalUsuarios,
            "lista_usuarios" => $datoUsuarios,
            "asignado" => $asignado,
        );

        $transport = $this->getServiceLocator()->get('mail.transport');
        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        $content = $renderer->render($plantilla, ['contenido' => $mailContent]);

        $messageEmail = new Message();
        $messageEmail->addTo($email)
            ->addFrom('puntos@beneficios.pe', 'Beneficios.pe')
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
        $content = $renderer->render('mail-asignaciones-admin', ['contenido' => $mailContent]);

        $messageEmail = new Message();
        $messageEmail->addTo($email)
            ->addFrom('asignacion@beneficios.pe', 'Beneficios.pe')
            ->setSubject('Asignacion de Puntos');

        $htmlBody = new MimePart($content);
        $htmlBody->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlBody));
        $messageEmail->setBody($body);
        $transport->send($messageEmail);
    }

    public function enviarCorreoReferido($cliente, $total, $email)
    {
        /*
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
        $content = $renderer->render('mail-asignaciones-admin', ['contenido' => $mailContent]);

        $messageEmail = new Message();
        $messageEmail->addTo($email)
            ->addFrom('asignacion@beneficios.pe', 'Beneficios.pe')
            ->setSubject('Puntos gratis asignados);

        $htmlBody = new MimePart($content);
        $htmlBody->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlBody));
        $messageEmail->setBody($body);
        $transport->send($messageEmail);*/
    }

    public function validarCampos($request)
    {
        $approved = false;
        $messages = array();
        $validAlnum = new Regex(array('pattern' => "/^([a-zA-Z0-9\/\-])+$/"));
        $validDigits = new IsInt();
        $validNotEmpty = new NotEmpty(NotEmpty::ALL);

        $numeroDocumento = $request->getPost()->numeroDocumento;
        $sumaPuntos = $request->getPost()->sumaPuntos;
        $restaPuntos = $request->getPost()->restaPuntos;

        if (count($numeroDocumento) == count($sumaPuntos) and count($sumaPuntos) == count($restaPuntos)) {
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

            //Validar Cantidad de Puntos a Sumar
            $sumaPuntosState = true;
            if (is_array($sumaPuntos) || is_object($sumaPuntos)) {
                foreach ($sumaPuntos as $value) {
                    $value = trim($value);
                    if (!$validDigits($value) && $value != "") {
                        $messages['sumaPuntos'][] = "El campo solo acepta números enteros.";
                        $sumaPuntosState = false;
                    } else {
                        $messages['sumaPuntos'][] = "";
                    }
                }
            } else {
                $sumaPuntosState = false;
            }

            //Validar Cantidad de Puntos a Restar
            $restaPuntosState = true;
            if (is_array($restaPuntos) || is_object($restaPuntos)) {
                foreach ($restaPuntos as $value) {
                    $value = trim($value);
                    if (!$validDigits($value) && $value != "") {
                        $messages['restaPuntos'][] = "El campo solo acepta números enteros.";
                        $restaPuntosState = false;
                    } else {
                        $messages['restaPuntos'][] = "";
                    }
                }
            } else {
                $restaPuntosState = false;
            }

            //Comprobando validaciones
            if ($numeroDocumentoState and $sumaPuntosState and $restaPuntosState) {
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
