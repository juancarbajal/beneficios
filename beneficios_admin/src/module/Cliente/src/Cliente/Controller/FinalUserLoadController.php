<?php

namespace Cliente\Controller;

use Cliente\Model\EmpresaSegmentoCliente;
use Cliente\Model\EmpresaSubgrupoCliente;
use Cliente\Model\Filter\FinalCsvFilter;
use Cliente\Form\FinalCsvForm;
use Cliente\Model\Preguntas;
use Empresa\Model\EmpresaSegmento;
use Zend\File\Transfer\Adapter\Http;
use Zend\I18n\Validator\Alnum;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Digits;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;

class FinalUserLoadController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;

    #region ObjectTables
    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getSegmentoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SegmentoTable');
    }

    public function getSubGrupoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SubGrupoTable');
    }

    public function getEmpresaSegmentoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaSegmentoTable');
    }

    public function getEmpresaSubgrupoTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaSubgrupoTable');
    }

    public function getEmpresaSegmentoClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaSegmentoClienteTable');
    }

    public function getEmpresaSubgrupoClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaSubgrupoClienteTable');
    }

    public function getEmpresaCliente()
    {
        return $this->serviceLocator->get('Cliente\Model\EmpresaClienteClienteTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Cliente\Model\ClienteTable');
    }

    public function getPreguntas()
    {
        return $this->serviceLocator->get('Cliente\Model\Table\PreguntasTable');
    }

    #endregion

    public function loadAction()
    {
        $fileMessage = '';
        $successMessage = '';
        $errorMessageCsv = array();

        $empresasData = array();
        $empresasFilter = array();
        $listaSegmentos = array('A', 'B', 'C', 'Z');
        $listaSubgrupos = array();

        $errorCsv = false;
        $errorDni = false;
        $errorPasaporte = false;
        $errorValid = false;

        $errorValidAnioNacimeinto = false;


        $countValid = 0;
        $nombre_empresa = null;

        try {
            $dataEmpresas = $this->getEmpresaTable()->getEmpresasCliente();
            foreach ($dataEmpresas as $e) {
                $empresasData[$e->id] = $e->NombreComercial . " - " . $e->RazonSocial . " - " . $e->Ruc;
                $empresasFilter[] = $e->id;
            }
        } catch (\Exception $ex) {
            $empresasFilter = array();
        }

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $empresasData[$empresa_value];
            $form = new FinalCsvForm('upload', $empresa_value, $tipo_usuario);
        } else {
            $form = new FinalCsvForm('upload', $empresasData);
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $filter = new FinalCsvFilter();

            if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                $form->setInputFilter($filter->getInputFilter(array($empresa_value)));
            } else {
                $form->setInputFilter($filter->getInputFilter($empresasFilter));
            }

            $form->setData($request->getPost());

            if ($form->isValid()) {
                $idEmpresa = $this->getRequest()->getPost()->empresa;
                $empresa = $this->getEmpresaTable()->getEmpresa($idEmpresa);
                //Lista de Subgrupos de la Empresa Seleccionada
                $subgrupos = $this->getSubGrupoTable()->getSubgrupoEmpresa($idEmpresa);
                foreach ($subgrupos as $value) {
                    $listaSubgrupos[$value->id] = trim($value->Nombre);
                }

                $fileData = $this->params()->fromFiles('file_csv');
                //Valida el tamaño del archivo
                $adapter = new Http();
                $sizeValidator = new Size(array('max' => 2097152)); //tamaño maximo en bytes
                $adapter->setValidators(array($sizeValidator), $fileData['name']);
                //Valida la extension del archivo
                $adapter2 = new Http();
                $extensionValidator = new Extension(array('extension' => array('xls', 'xlsx')), true);
                $adapter2->setValidators(array($extensionValidator), $fileData['name']);

                if (!$adapter->isValid() || !$adapter2->isValid()) {
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

                    $dataCsv = array();
                    if ($highestColumm == "D" || $highestColumm == "E" || $highestColumm == "F" || $highestColumm == "G") {
                        foreach ($objWorksheet->getRowIterator(1) as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            $errorRepeat = false;
                            $rowIndex = $row->getRowIndex();
                            if ($rowIndex > 1) {
                                $data_cell = array();
                                foreach ($cellIterator as $cell) {
                                    array_push($data_cell, (string)$cell->getValue());
                                }

                                $rowError = "Registro #" . ($rowIndex - 1) . ". ";

                                if ($data_cell[6] != '') {
                                    if (!is_numeric($data_cell[6]) || strlen($data_cell[6])!=4) {
                                        $errorValidAnioNacimeinto = true;
                                        $errorMessageCsv[$rowIndex] = $rowError . 'El campo año de nacimiento no tiene el formato valido';

                                    }
                                }

                                if ($data_cell[0] == '') {
                                    $errorValid = true;
                                    $errorMessageCsv[$rowIndex] = $rowError . 'Falta ingresar número de documento.';
                                } else {
                                    if (array_key_exists($data_cell[0], $dataCsv)) {
                                        $errorRepeat = true;
                                        //$errorMessageCsv[$rowIndex] = $rowError . 'Número. de documento repetido.';
                                    } else {
                                        if (strtoupper(trim($data_cell[1])) == "DNI") {
                                            $validNumb = new Digits();
                                            if ($validNumb->isValid($data_cell[0])) {
                                                if (strlen($data_cell[0]) != 8) {
                                                    $errorDni = true;
                                                    $errorMessageCsv[$rowIndex] = $rowError .
                                                        'Error en la longitud del DNI.';
                                                }
                                            } else {
                                                $errorDni = true;
                                                $errorMessageCsv[$rowIndex] = $rowError . 'DNI no válido.';
                                            }
                                        } elseif (strtoupper(trim($data_cell[1])) == "PASAPORTE") {
                                            $valid = new Alnum();
                                            if ($valid->isValid($data_cell[0])) {
                                                if (strlen($data_cell[0]) > 12 or strlen($data_cell[0]) < 5) {
                                                    $errorPasaporte = true;
                                                    $errorMessageCsv[$rowIndex] = $rowError .
                                                        'Error en la longitud del Pasaporte.';
                                                }
                                            } else {
                                                $errorPasaporte = true;
                                                $errorMessageCsv[$rowIndex] = $rowError . 'Pasaporte no válido.';
                                            }
                                        } else {
                                            $data_cell[1] = "Otros";
                                            $valid = new Alnum();
                                            if (strlen($data_cell[0]) < 5 or !$valid->isValid($data_cell[0])) {
                                                $errorPasaporte = true;
                                                $errorMessageCsv[$rowIndex] = $rowError .
                                                    'Error de formato del Documento Otros.';
                                            }
                                        }
                                    }

                                    if ($errorRepeat == false) {
                                        //Valida los Segmentos
                                        $data_cell[2] = strtoupper(trim($data_cell[2]));
                                        if (trim($data_cell[2]) == '') {
                                            $data_cell[2] = 'Z';
                                            if (!in_array($data_cell[2], $listaSegmentos)) {
                                                $errorValid = true;
                                                $errorMessageCsv[$rowIndex] =
                                                    (isset($errorMessageCsv[$rowIndex]))
                                                        ? $errorMessageCsv[$rowIndex] . ' El Segmento ' .
                                                        $data_cell[2] . ' no es válido.'
                                                        : $rowError . ' El Segmento ' . $data_cell[2] .
                                                        ' no es válido.';
                                            }
                                        } elseif (!in_array($data_cell[2], $listaSegmentos)) {
                                            $errorValid = true;
                                            $errorMessageCsv[$rowIndex] =
                                                (isset($errorMessageCsv[$rowIndex]))
                                                    ? $errorMessageCsv[$rowIndex] . ' El Segmento ' .
                                                    $data_cell[2] . ' no es válido.'
                                                    : $rowError . ' El Segmento ' . $data_cell[2] . ' no es válido.';
                                        }
                                        //Valida los Subgrupos
                                        $data_cell[3] = trim($data_cell[3]);
                                        if (!empty($listaSubgrupos) && $empresa->ClaseEmpresaCliente == 'Especial') {
                                            if (!in_array($data_cell[3], $listaSubgrupos)) {
                                                $errorValid = true;
                                                $errorMessageCsv[$rowIndex] =
                                                    (isset($errorMessageCsv[$rowIndex]))
                                                        ? $errorMessageCsv[$rowIndex] . ' El Subgrupo ' .
                                                        $data_cell[3] . ' no es válido.'
                                                        : $rowError . ' El Subgrupo ' .
                                                        $data_cell[3] . ' no es válido.';
                                            }
                                        }
                                        //Guardamos los Datos
                                        $dataCsv[$data_cell[0]] = $data_cell;
                                    }
                                }
                            }
                        }
                    } else {
                        $errorCsv = true;
                        $errorMessageCsv[] = 'Formato del documento incorrecto, por favor revise la plantilla.';
                    }

                    if (!$errorCsv && !$errorDni && !$errorValid && !$errorPasaporte && !$errorValidAnioNacimeinto) {
                        foreach ($dataCsv as $value) {
//                            var_dump($value);exit;
                            //Verifica si el cliente existe por el numero de documento
                            if ($this->getClienteTable()->getClientIfExist($value[0])) {
                                $dataCliente = $this->getClienteTable()->getClientByDoc($value[0]);

                                //Verificar Relacion Empresa Cliente
                                $dataEmpresaCliente = $this->getEmpresaCliente()
                                    ->searchEmpresaCliente($empresa->id, $dataCliente->id);

                                //Establecemos el tipo de documento del cliente
                                if (strtoupper(trim($value[1])) == "DNI") {
                                    $tipo = 1;
                                } elseif (strtoupper(trim($value[1])) == "PASAPORTE") {
                                    $tipo = 2;
                                } else {
                                    $tipo = 3;
                                }

                                //Guardamos los datos

                                $dataActualizar=[];
                                $dataActualizar['BNF_TipoDocumento_id']=$tipo;
                                $dataActualizar['FechaActualizacion']=date('Y-m-d H:i:s');
                                if(isset($value[4]) && $value[4]!=''){
                                    $dataActualizar['Nombre']=$value[4];
                                }
                                if(isset($value[5]) && $value[5]!=''){
                                    $dataActualizar['Apellido']=$value[5];
                                }

                                $this->getClienteTable()->update(
                                    $dataActualizar,
                                    $dataCliente->id
                                );

                                if ($dataEmpresaCliente) {
                                    $this->getEmpresaCliente()->updateByEmpresaAndClient(
                                        array(
                                            'Estado' => 'Activo',
                                            'Eliminado' => 0
                                        ),
                                        $empresa->id,
                                        $dataCliente->id
                                    );
                                } else {
                                    $this->getEmpresaCliente()->insert(
                                        array(
                                            'BNF_Empresa_id' => $empresa->id,
                                            'BNF_Cliente_id' => $dataCliente->id,
                                            'Estado' => 'Activo',
                                            'Eliminado' => 0
                                        )
                                    );
                                }

                                //Recuperamos el Segmento por el nombre
                                $segmento = $this->getSegmentoTable()->getByNombre($value[2]);

                                //Verificamos la relacion de Empresa-Segmento
                                if ($this->getEmpresaSegmentoTable()
                                    ->getEmpresaSegmentoIfExist($empresa->id, $segmento->id)
                                ) {
                                    $empresaSegmento = $this->getEmpresaSegmentoTable()
                                        ->getEmpresaSegmentoDatos($empresa->id, $segmento->id);
                                } else {
                                    $empresaSegmento = new EmpresaSegmento();
                                    $empresaSegmento->BNF_Empresa_id = $empresa->id;
                                    $empresaSegmento->BNF_Segmento_id = $segmento->id;
                                    $empresaSegmento->Eliminado = 0;

                                    $empresaSegmento->id = $this->getEmpresaSegmentoTable()
                                        ->saveEmpresaSegmento($empresaSegmento);
                                }

                                $resp = $this->getEmpresaSegmentoClienteTable()
                                    ->getEmpresaSegmentoClienteCurrent($empresa->id, $dataCliente->id);

                                $empSegCli = new EmpresaSegmentoCliente();

                                if (is_object($resp)) {
                                    $empSegCli->idBNF_EmpresaSegmentoCliente = $resp->idBNF_EmpresaSegmentoCliente;
                                    $empSegCli->BNF_EmpresaSegmento_id = $empresaSegmento->id;
                                    $empSegCli->BNF_Cliente_id = $dataCliente->id;
                                    $empSegCli->Eliminado = '0';
                                    $this->getEmpresaSegmentoClienteTable()
                                        ->saveEmpresaSegmentoCliente($empSegCli);
                                } else {
                                    $empSegCli->BNF_EmpresaSegmento_id = $empresaSegmento->id;
                                    $empSegCli->BNF_Cliente_id = $dataCliente->id;
                                    $empSegCli->Eliminado = '0';
                                    $this->getEmpresaSegmentoClienteTable()
                                        ->saveEmpresaSegmentoCliente($empSegCli);
                                }

                                //Registrar Relacion EmpresaSubgrupoCliente
                                if ($empresa->ClaseEmpresaCliente == 'Especial') {
                                    $idEmpresaSubgrupo = array_search($value[3], $listaSubgrupos);

                                    $resp = $this->getEmpresaSubgrupoClienteTable()
                                        ->getEmpresaSubgrupoClienteCurrent($empresa->id, $dataCliente->id);

                                    $empSubCli = new EmpresaSubgrupoCliente();
                                    if (count($resp) > 0) {
                                        $empSubCli->idBNF_EmpresaSubgrupoCliente = $resp->idBNF_EmpresaSubgrupoCliente;
                                        $empSubCli->BNF_Subgrupo_id = $idEmpresaSubgrupo;
                                        $empSubCli->BNF_Cliente_id = $dataCliente->id;
                                        $empSubCli->Eliminado = '0';
                                        $this->getEmpresaSubgrupoClienteTable()
                                            ->saveEmpresaSubgrupoCliente($empSubCli);
                                    } else {
                                        $empSubCli->BNF_Subgrupo_id = $idEmpresaSubgrupo;
                                        $empSubCli->BNF_Cliente_id = $dataCliente->id;
                                        $empSubCli->Eliminado = '0';
                                        $this->getEmpresaSubgrupoClienteTable()
                                            ->saveEmpresaSubgrupoCliente($empSubCli);
                                    }
                                }
                            } else {
                                //Establecemos el tipo de documento del cliente
                                if (strtoupper(trim($value[1])) == "DNI") {
                                    $tipo = 1;
                                } elseif (strtoupper(trim($value[1])) == "PASAPORTE") {
                                    $tipo = 2;
                                } else {
                                    $tipo = 3;
                                }

                                //Guardamos los datos

                                $dataRegistrar=[];
                                $dataRegistrar['BNF_TipoDocumento_id']=$tipo;
                                $dataRegistrar['NumeroDocumento']=$value[0];
                                $dataRegistrar['FechaCreacion']=date('Y-m-d H:i:s');
                                if(isset($value[4]) && $value[4]!=''){
                                    $dataRegistrar['Nombre']=$value[4];
                                }
                                if(isset($value[5]) && $value[5]!=''){
                                    $dataRegistrar['Apellido']=$value[5];
                                }
                                $idClient = $this->getClienteTable()->insert(
                                    $dataRegistrar
                                );

                                //Creamos la relacion con Preguntas
                                $pregunta = new Preguntas();
                                $pregunta->BNF_Cliente_id = $idClient;

                                if(isset($value[4]) && $value[4]!=''){
                                    $pregunta->Pregunta01 =$value[4];
                                }
                                if(isset($value[5]) && $value[5]!=''){
                                    $pregunta->Pregunta02 =$value[5];
                                }
                                if(isset($value[6]) && $value[6]!=''){
                                    $pregunta->Pregunta03 =$value[6];
                                }
                                $this->getPreguntas()->savePreguntasConMasDatos($pregunta);

                                //Creamos la relacion con EmpresaCliente
                                $this->getEmpresaCliente()->insert(
                                    array(
                                        'BNF_Empresa_id' => $idEmpresa,
                                        'BNF_Cliente_id' => $idClient,
                                        'Estado' => 'Activo',
                                        'Eliminado' => 0
                                    )
                                );

                                //Recuperamos el Segmento por el nombre
                                $segmento = $this->getSegmentoTable()->getByNombre($value[2]);

                                //Verificamos la relacion de Empresa-Segmento
                                if ($this->getEmpresaSegmentoTable()
                                    ->getEmpresaSegmentoIfExist($empresa->id, $segmento->id)
                                ) {
                                    $empresaSegmento = $this->getEmpresaSegmentoTable()
                                        ->getEmpresaSegmentoDatos($empresa->id, $segmento->id);
                                } else {
                                    $empresaSegmento = new EmpresaSegmento();
                                    $empresaSegmento->BNF_Empresa_id = $empresa->id;
                                    $empresaSegmento->BNF_Segmento_id = $segmento->id;
                                    $empresaSegmento->Eliminado = 0;

                                    $empresaSegmento->id = $this->getEmpresaSegmentoTable()
                                        ->saveEmpresaSegmento($empresaSegmento);
                                }

                                //Creamos la relacion ClienteSegmento
                                $empSegCli = new EmpresaSegmentoCliente();
                                $empSegCli->BNF_EmpresaSegmento_id = $empresaSegmento->id;
                                $empSegCli->BNF_Cliente_id = $idClient;
                                $empSegCli->Eliminado = '0';
                                $this->getEmpresaSegmentoClienteTable()
                                    ->saveEmpresaSegmentoCliente($empSegCli);

                                //Creamos la relacion ClienteSubgrupo
                                if ($empresa->ClaseEmpresaCliente == 'Especial') {
                                    $idEmpresaSubgrupo = array_search($value[3], $listaSubgrupos);
                                    $empSubCli = new EmpresaSubgrupoCliente();
                                    $empSubCli->BNF_Subgrupo_id = $idEmpresaSubgrupo;
                                    $empSubCli->BNF_Cliente_id = $idClient;
                                    $empSubCli->Eliminado = '0';
                                    $this->getEmpresaSubgrupoClienteTable()
                                        ->saveEmpresaSubgrupoCliente($empSubCli);
                                }
                            }
                            $countValid++;
                        }
                        $successMessage = 'Se registraron un total de ' . $countValid . ' Usuarios Finales.';
                    }
                }
            }
        }

        return new ViewModel(
            array(
                'final' => 'active',
                'fload' => 'active',
                'fileMessage' => $fileMessage,
                'successMessage' => $successMessage,
                'errorMessageCsv' => $errorMessageCsv,
                'errorCsv' => $errorCsv,
                'errorDni' => $errorDni,
                'errorPasaporte' => $errorPasaporte,
                'errorValidAnioNacimeinto'=> $errorValidAnioNacimeinto,
                'errorValid' => $errorValid,
                'form' => $form,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }

    public function disabledAction()
    {
        $fileMessage = '';
        $successMessage = '';
        $errorMessageCsv = array();

        $empresasData = array();
        $empresasFilter = array();

        $errorCsv = false;
        $errorValid = false;
        $countValid = 0;
        $nombre_empresa = null;

        try {
            $dataEmpresas = $this->getEmpresaTable()->getEmpresasCliente();
            foreach ($dataEmpresas as $e) {
                $empresasData[$e->id] = $e->NombreComercial . " - " . $e->RazonSocial . " - " . $e->Ruc;
                $empresasFilter[] = $e->id;
            }
        } catch (\Exception $ex) {
            $empresasFilter = array();
        }

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $empresasData[$empresa_value];
            $form = new FinalCsvForm('upload', $empresa_value, $tipo_usuario);
        } else {
            $form = new FinalCsvForm('upload', $empresasData);
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $filter = new FinalCsvFilter();

            if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                $form->setInputFilter($filter->getInputFilter(array($empresa_value)));
            } else {
                $form->setInputFilter($filter->getInputFilter($empresasFilter));
            }

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $idEmpresa = $this->getRequest()->getPost()->empresa;
                $empresa = $this->getEmpresaTable()->getEmpresa($idEmpresa);

                $fileData = $this->params()->fromFiles('file_csv');
                //Valida el tamaño del archivo
                $adapter = new Http();
                $sizeValidator = new Size(array('max' => 2097152)); //tamaño maximo en bytes
                $adapter->setValidators(array($sizeValidator), $fileData['name']);
                //Valida la extension del archivo
                $adapter2 = new Http();
                $extensionValidator = new Extension(array('extension' => array('xls', 'xlsx')), true);
                $adapter2->setValidators(array($extensionValidator), $fileData['name']);

                if (!$adapter->isValid() || !$adapter2->isValid()) {
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
                    //$highestRow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                    $dataCsv = array();
                    if ($highestColumm == "A") {
                        foreach ($objWorksheet->getRowIterator(1) as $row) {
                            $cliente_empresa_exist = false;

                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            $rowIndex = $row->getRowIndex();
                            if ($rowIndex > 1) {
                                $data_cell = array();
                                foreach ($cellIterator as $cell) {
                                    array_push($data_cell, (string)$cell->getValue());
                                }

                                if ($data_cell[0] == '') {
                                    $errorValid = true;
                                    $errorMessageCsv[$rowIndex] = 'falta ingresar número de documento en la fila.'
                                        . ($rowIndex - 1);
                                } else {
                                    if (!array_key_exists($data_cell[0], $dataCsv)) {
                                        $cliente_exist = $this->getClienteTable()->getClientByDoc($data_cell[0]);

                                        if ($cliente_exist) {
                                            $cliente_empresa_exist = $this->getEmpresaCliente()
                                                ->searchEmpresaCliente($idEmpresa, $cliente_exist->id);
                                        }

                                        if ($cliente_empresa_exist) {
                                            $dataCsv[$data_cell[0]] = $data_cell;
                                        } else {
                                            $errorCsv = true;
                                            $errorMessageCsv[] = 'El Número de Documento de la fila ' . ($rowIndex - 1)
                                                . ' que intenta desactivar no existe.';
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $errorCsv = true;
                        $errorMessageCsv[] = 'Formato del documento incorrecto, por favor revise la plantilla.';
                    }

                    if (!$errorCsv && !$errorValid) {
                        foreach ($dataCsv as $value) {
                            $dataCliente = $this->getClienteTable()->getClientByDoc($value[0]);

                            //Verificar Relacion Empresa Cliente
                            $dataEmpresaCliente = $this->getEmpresaCliente()
                                ->searchEmpresaCliente($empresa->id, $dataCliente->id);

                            if ($dataEmpresaCliente) {
                                $this->getEmpresaCliente()->updateByEmpresaAndClient(
                                    array(
                                        'Estado' => 'Inactivo'
                                    ),
                                    $empresa->id,
                                    $dataCliente->id
                                );
                            }
                            $countValid++;
                        }
                        $successMessage = 'Fueron desactivados un total de ' . $countValid . ' Usuarios Finales.';
                    }
                }
            }
        }

        return new ViewModel(
            array(
                'final' => 'active',
                'fdisabled' => 'active',
                'fileMessage' => $fileMessage,
                'successMessage' => $successMessage,
                'errorMessageCsv' => $errorMessageCsv,
                'errorCsv' => $errorCsv,
                'errorValid' => $errorValid,
                'form' => $form,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }
}
