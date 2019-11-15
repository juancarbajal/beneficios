<?php

namespace Referido\Controller;

use Puntos\Model\Asignacion;
use Puntos\Model\AsignacionEstadoLog;
use Referido\Model\Filter\FinalCsvFilter;
use Referido\Form\FinalCsvForm;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Regex;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;

class FinalUserLoadController extends AbstractActionController
{
    const USUARIO_REFERIDO = 8;
    const OPERACION_SUMAR = "Sumar";
    const OPERACION_ASIGNAR = "Asignar";

    #region ObjectTables
    public function getCampaniaPTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\CampaniasPTable');
    }

    public function getSegmentosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\SegmentosPTable');
    }

    public function getEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaClienteClienteTable');
    }

    public function getConfiguracionReferidosTable()
    {
        return $this->serviceLocator->get('Referido\Model\Table\ConfiguracionReferidosTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\ClienteTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\AsignacionTable');
    }

    public function getAsignacionEstadoLogTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\AsignacionEstadoLogTable');
    }

    public function getClienteLandingTable()
    {
        return $this->serviceLocator->get('Referido\Model\Table\ClienteLandingTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    #endregion

    public function loadAction()
    {
        $fileMessage = '';
        $successMessage = '';
        $errorMessageCsv = array();

        $campaniasData = array();
        $campaniasFilter = array();

        $errorCsv = false;
        $errorDni = false;
        $errorValid = false;
        $countValid = 0;
        $nombre_empresa = null;
        $empresa_value = ($this->identity()->BNF_Empresa_id) ? $this->identity()->BNF_Empresa_id : 369;

        try {
            $dataCampanias = $this->getCampaniaPTable()->getCampaniasPByEmpresaPersonalizada($empresa_value);
            foreach ($dataCampanias as $c) {
                $campaniasData[$c->id] = $c->NombreCampania;
                $campaniasFilter[] = $c->id;
            }
        } catch (\Exception $ex) {
            $campaniasFilter = array();
        }

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $form = new FinalCsvForm('upload', $campaniasData);

        $request = $this->getRequest();


        if ($request->isPost()) {
            $filter = new FinalCsvFilter();

            $form->setInputFilter($filter->getInputFilter($campaniasFilter));

            $form->setData($request->getPost());

            if ($form->isValid()) {
                $idCampania = $this->getRequest()->getPost()->campania;
                $segmento = $this->getSegmentosTable()->getByName($idCampania, 'Personalizada');
                $datoSegmento = $this->getSegmentosTable()->getDetalleSegmentoAsignacion($segmento->id);

                $fileData = $this->params()->fromFiles('file_csv');

                //Valida el tamaño del archivo
                $sizeValidator = new Size(array('max' => 2097152)); //tamaño maximo en bytes

                //Valida la extension del archivo
                $extensionValidator = new Extension(array('extension' => array('xls', 'xlsx')), true);

                $listaDniRepetidos = array();
                $presupuesto = $datoSegmento->Subtotal - $datoSegmento->AsignadoActivo
                    - $datoSegmento->AsignadoEliminado - $datoSegmento->AplicadoInactivo;

                if (!$sizeValidator->isValid($fileData) || !$extensionValidator->isValid($fileData)) {
                    if ($fileData['name'] == '') {
                        $fileMessage = 'Debe seleccionar un archivo';
                    } else {
                        $fileMessage = 'Archivo no válido';
                    }
                } else {
                    $inputFileName = $fileData['tmp_name'];
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $objPHPExcel = $objReader->load($inputFileName);

                    $objWorksheet = $objPHPExcel->setActiveSheetIndex();

                    $highestColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                    #region Validar Datos
                    $dataCsv = array();
                    if ($highestColumm == "J") {
                        foreach ($objWorksheet->getRowIterator(2) as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);

                            $rowIndex = $row->getRowIndex();
                            $dataCell = array();
                            foreach ($cellIterator as $cell) {
                                array_push($dataCell, (string)$cell->getValue());
                            }

                            $registro = "#" . $rowIndex . ". ";
                            $dataCell[9] = trim($dataCell[9]);
                            if ($dataCell[9] == '') {
                                $errorDni = true;
                            } else {
                                if (array_key_exists($dataCell[9], $dataCsv)) {
                                    if (array_key_exists($dataCell[9], $listaDniRepetidos)) {
                                        $listaDniRepetidos[$dataCell[9]]++;
                                    } else {
                                        $listaDniRepetidos[$dataCell[9]] = 2;
                                    }
                                } else {
                                    $valid = new  Regex(array('pattern' => "/^(([a-zA-Z0-9]+(\-)?(\/)?)+){8,15}$/"));
                                    if ($valid->isValid($dataCell[9])) {
                                        //Verifica si el cliente fue registrado por Landing
                                        if ($this->getClienteLandingTable()->getClientByDoc($dataCell[9])) {
                                            //Verifica si el cliente pertenece a la empresa
                                            if ($this->getEmpresaClienteTable()
                                                ->searchEmpresaClientebyDoc($empresa_value, $dataCell[9])
                                            ) {
                                                //Verifica que el cliente esté asociado a la empresa y esté activo
                                                if (!$this->getEmpresaClienteTable()
                                                    ->searchEmpresaClienteActive($empresa_value, $dataCell[9])
                                                ) {
                                                    $errorDni = true;
                                                    $errorMessageCsv[$rowIndex] = "El documento en la fila " . $registro
                                                        . " no está activo";
                                                }
                                            } else {
                                                $errorDni = true;
                                                $errorMessageCsv[$rowIndex] = "El usuario de la fila " . $registro
                                                    . " no pertenece a verisure";
                                            }
                                        } else {
                                            $errorDni = true;
                                            $errorMessageCsv[] = "El usuario de la fila " . $registro
                                                . " no ha registrado referidos";
                                        }
                                    } else {
                                        $errorDni = true;
                                        $errorMessageCsv[$rowIndex] = 'El documento ' . $registro .
                                            " ingresado no es un documento válido." . "<br>";
                                    }
                                }
                            }

                            if (!$errorDni) {
                                $dataCsv[$dataCell[9]] = $dataCell[9];
                            }
                        }
                    } else {
                        $errorCsv = true;
                        $errorMessageCsv[] = 'Formato del documento incorrecto, por favor revise la plantilla.';
                    }

                    $errorPresupuesto = false;
                    $conf_referidos = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByTipo('puntos');
                    $repetidos = [];
                    foreach ($conf_referidos as $value) {
                        $repetidos[$value->Campo] = $value->Atributo;
                    }

                    $total = 0;
                    foreach ($dataCsv as $key => $dni) {
                        // Puntos a repartir verificando presupuesto
                        if (!empty($repetidos)) {
                            if (array_key_exists($dni, $listaDniRepetidos)) {
                                $nroVeces = $listaDniRepetidos[$dni];

                                if (array_key_exists($nroVeces, $repetidos)) {
                                    $total += $repetidos[$nroVeces];
                                } else {
                                    $total += $repetidos['3'];
                                }
                            } else {
                                $total += $repetidos['1'];
                            }
                        } else {
                            $total += 0;
                        }

                        if ($total > $presupuesto) {
                            $errorValid = true;
                            $errorMessageCsv[] = 'Campaña no tiene presupuesto suficiente.';
                            break;
                        }
                    }

                    #endregion
                    if (!$errorCsv && !$errorValid && !$errorPresupuesto) {
                        foreach ($dataCsv as $value) {
                            // Puntos a repartir
                            if (!empty($repetidos)) {
                                if (array_key_exists($value, $listaDniRepetidos)) {
                                    $nroVeces = $listaDniRepetidos[$value];

                                    if (array_key_exists($nroVeces, $repetidos)) {
                                        $puntos = $repetidos[$nroVeces];
                                    } else {
                                        $puntos = $repetidos['3'];
                                    }
                                } else {
                                    $puntos = $repetidos['1'];
                                }
                            } else {
                                $puntos = 0;
                            }
                            $dataCliente = $this->getClienteTable()->getClientByDoc($value);
                            $dataAsignacion = $this->getAsignacionTable()
                                ->getAsignacionCliente($datoSegmento->id, $dataCliente->id);

                            $motivo = "Agregando Puntos Referido";
                            if ($dataAsignacion) {
                                if ($dataAsignacion->EstadoPuntos == "Cancelado") {
                                    $dataAsignacion->EstadoPuntos = "Activado";
                                }
                                $asignacionEstadoLog = new AsignacionEstadoLog();
                                $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $dataAsignacion->id;
                                $asignacionEstadoLog->BNF2_Segmento_id = $dataAsignacion->BNF2_Segmento_id;
                                $asignacionEstadoLog->BNF_Cliente_id = $dataAsignacion->BNF_Cliente_id;
                                $asignacionEstadoLog->TipoAsignamiento = 'Referido';
                                $asignacionEstadoLog->CantidadPuntos = $dataAsignacion->CantidadPuntos + $puntos;
                                $asignacionEstadoLog->CantidadPuntosUsados = (int)$dataAsignacion->CantidadPuntosUsados;
                                $asignacionEstadoLog->CantidadPuntosDisponibles = $dataAsignacion->CantidadPuntosDisponibles + (int)$puntos;
                                $asignacionEstadoLog->CantidadPuntosEliminados = (int)$dataAsignacion->CantidadPuntosEliminados;
                                $asignacionEstadoLog->EstadoPuntos = $dataAsignacion->EstadoPuntos;
                                $asignacionEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                                $asignacionEstadoLog->Puntos = $puntos;
                                $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                $asignacionEstadoLog->Motivo = $motivo;
                                $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);

                                $dataAsignacion->BNF2_Segmento_id = $datoSegmento->id;
                                $dataAsignacion->CantidadPuntos =
                                    $dataAsignacion->CantidadPuntos + (int)$puntos;
                                $dataAsignacion->CantidadPuntosDisponibles =
                                    $dataAsignacion->CantidadPuntosDisponibles + (int)$puntos;
                                $dataAsignacion->Eliminado = 0;
                                $this->getAsignacionTable()->saveAsignacion($dataAsignacion);
                            } else {
                                $asignacion = new Asignacion();
                                $asignacion->BNF2_Segmento_id = $datoSegmento->id;
                                $asignacion->BNF_Cliente_id = $dataCliente->id;
                                $asignacion->TipoAsignamiento = 'Referido';
                                $asignacion->CantidadPuntos = (int)$puntos;
                                $asignacion->CantidadPuntosDisponibles = (int)$puntos;
                                $asignacion->EstadoPuntos = 'Activado';
                                $asignacion->Eliminado = 0;
                                $asignacionId = $this->getAsignacionTable()->saveAsignacion($asignacion);

                                $asignacionEstadoLog = new AsignacionEstadoLog();
                                $asignacionEstadoLog->BNF2_Asignacion_Puntos_id = $asignacionId;
                                $asignacionEstadoLog->BNF2_Segmento_id = $asignacion->BNF2_Segmento_id;
                                $asignacionEstadoLog->TipoAsignamiento = 'Referido';
                                $asignacionEstadoLog->BNF_Cliente_id = $asignacion->BNF_Cliente_id;
                                $asignacionEstadoLog->CantidadPuntos = $asignacion->CantidadPuntos;
                                $asignacionEstadoLog->CantidadPuntosUsados = 0;
                                $asignacionEstadoLog->CantidadPuntosDisponibles = $asignacion->CantidadPuntosDisponibles;
                                $asignacionEstadoLog->CantidadPuntosEliminados = 0;
                                $asignacionEstadoLog->EstadoPuntos = $asignacion->EstadoPuntos;
                                $asignacionEstadoLog->Operacion = $this::OPERACION_ASIGNAR;
                                $asignacionEstadoLog->Puntos = $asignacion->CantidadPuntos;
                                $asignacionEstadoLog->Motivo = $motivo;
                                $asignacionEstadoLog->BNF_Usuario_id = $this->identity()->id;
                                $this->getAsignacionEstadoLogTable()->saveAsignacionEstadoLog($asignacionEstadoLog);
                            }
                            $countValid++;

                            $email = $this->getClienteLandingTable()
                                ->getClientByDoc($dataCliente->NumeroDocumento)->Email;

                            $empresa = $this->getEmpresaTable()->getEmpresa($empresa_value);
                            $config = $this->getServiceLocator()->get('Config');
                            $web = $config['URL_WEB'];
                            $parse_url = parse_url($web);
                            $web = $parse_url['scheme'] . "://" . ((!empty($empresa->Slug)) ? $empresa->Slug . "." : '')
                                . $parse_url['host'] . $parse_url['path'] . "puntos";

                            $cliente_landing = $this->getClienteLandingTable()->getClientByDoc($value);

                            if (trim($cliente_landing->Nombres_Apellidos) != "") {
                                $nombre = $cliente_landing->Nombres_Apellidos;
                            } else {
                                $nombre = $dataCliente->Nombre . (!empty($dataCliente->Apellido) ? " " . $dataCliente->Apellido : '');
                            }

                            $this->enviarCorreoReferido($puntos, $email, $web, $nombre);
                        }

                        $form = new FinalCsvForm('upload', $campaniasData);
                        $successMessage = $successMessage . 'Se Asignaron los puntos un total de ' .
                            $countValid . ' Usuarios Finales.';
                    }
                }
            }
        }
        return new ViewModel(
            array(
                'referido' => 'active',
                'uload' => 'active',
                'fileMessage' => $fileMessage,
                'successMessage' => $successMessage,
                'errorMessageCsv' => $errorMessageCsv,
                'form' => $form,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }

    public function enviarCorreoReferido($total, $email, $web, $nombre)
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }


        $mailContent = array(
            "total" => $total,
            "url_web" => $web,
            "cliente" => $nombre,
        );

        $transport = $this->getServiceLocator()->get('mail.transport');
        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        $content = $renderer->render('mail-notificacion-puntos', ['contenido' => $mailContent]);

        $messageEmail = new Message();
        $messageEmail->addTo($email)
            ->addFrom('asignacion@beneficios.pe', 'Beneficios.pe')
            ->setSubject('Verisure te regala puntos para canjear GRANDES PREMIOS');

        $htmlBody = new MimePart($content);
        $htmlBody->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($htmlBody));
        $messageEmail->setBody($body);
        $transport->send($messageEmail);
    }
}
