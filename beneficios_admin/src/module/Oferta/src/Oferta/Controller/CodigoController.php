<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/12/16
 * Time: 06:35 PM
 */

namespace Oferta\Controller;


use Oferta\Form\RegistrarCodigosForm;
use Oferta\Model\Data\RegistrarCodigoData;
use Oferta\Model\Filter\CargarCodigosFilter;
use Oferta\Model\OfertaCuponCodigo;
use Zend\I18n\Validator\Alnum;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\View\Model\ViewModel;
use Zend\File\Transfer\Adapter\Http;

class CodigoController extends AbstractActionController
{
    protected $formData;
    protected $filterData;

    #region ObjectTables
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }

    public function getOfertaCodigoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaCuponCodigoTable');
    }

    public function getOfertaAtributosTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaAtributosTable');
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $errorCsv = false;
        $errorRepeat = false;
        $errorVacio = false;
        $errorCodigo = false;
        $errorCant = false;


        $stock = 0;
        $mensaje = null;
        $type = "danger";
        $datos = new RegistrarCodigoData($this);
        $form = new RegistrarCodigosForm('registrar-oferta', $datos->getFormData());

        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();

            $validate = new CargarCodigosFilter();
            $form->setInputFilter($validate->getInputFilter($datos->getFilterData(), $post));
            $form->setData($post);
            if ($form->isValid()) {
                #region Oferta
                $oferta = $this->getOfertaTable()->getOferta((int)$post['Oferta']);

                if($oferta->TipoAtributo == 'Split') {
                    $atributos = $this->getOfertaAtributosTable()->getAllOfertaAtributos($oferta->id);
                    foreach ($atributos as $value) {
                        $stock += $value->Stock;
                    }
                } else {
                    $stock = $oferta->Stock;
                }

                $cantAsignados = $this->getOfertaCodigoTable()->getCantByOferta($oferta->id);
                if($cantAsignados > 0) {
                    if ($stock <= $cantAsignados) {
                        $errorCant = true;
                        $mensaje[] = 'La oferta ya tiene codigos asignados';
                    } else {
                        $stock = $stock - $cantAsignados;
                    }
                }
                #endregion

                if (!$errorCant) {
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
                            $mensaje[] = 'Debe seleccionar un archivo';
                        } else {
                            $mensaje[] = 'Archivo no válido';
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
                        $countRegistros = 0;
                        if ($highestColumm == "A") {
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
                                    if ($data_cell[0] == '') {
                                        $errorVacio = true;
                                        $mensaje[$rowIndex] = $rowError . 'Falta ingresar Código.';
                                    } else {
                                        if (array_key_exists($data_cell[0], $dataCsv)) {
                                            $errorRepeat = true;
                                            $mensaje[$rowIndex] = $rowError . 'Número de Código repetido.';
                                        } elseif ($this->getOfertaCodigoTable()->getByCodigo($data_cell[0])) {
                                            $errorRepeat = true;
                                            $mensaje[$rowIndex] = $rowError . 'Número de Código repetido.';
                                        } else {
                                            $valid = new Alnum();
                                            if ($valid->isValid($data_cell[0])) {
                                                if (strlen($data_cell[0]) > 45) {
                                                    $errorCodigo = true;
                                                    $mensaje[$rowIndex] = $rowError .
                                                        'Error en la longitud del Código.';
                                                } else {
                                                    $dataCsv[] = $data_cell[0];
                                                    $countRegistros++;
                                                }
                                            } else {
                                                $errorCodigo = true;
                                                $mensaje[$rowIndex] = $rowError . 'Código no válido.';
                                            }
                                        }
                                    }
                                }
                            }

                            if ($countRegistros != $stock) {
                                $errorCant = true;
                                $mensaje[] = 'La cantidad de Codigos no coincide con el Stock';
                            }
                        } else {
                            $errorCsv = true;
                            $mensaje[] = 'Formato del documento incorrecto, por favor revise la plantilla.';
                        }


                        if (!$errorCsv && !$errorRepeat && !$errorVacio && !$errorCodigo && !$errorCant) {
                            $countValid = 0;
                            foreach ($dataCsv as $value) {
                                //Verifica si el cliente existe por el numero de documento
                                $codigo = $this->getOfertaCodigoTable()->getExistByCodigo($value[0]);
                                if ($codigo) {
                                    $this->getOfertaCodigoTable()->uodate($codigo->id, ['Estado' => '0']);
                                } else {
                                    $codigo = new OfertaCuponCodigo();
                                    $codigo->BNF_Oferta_id = $oferta->id;
                                    $codigo->Codigo = $value;
                                    $codigo->Estado = '0';
                                    $this->getOfertaCodigoTable()->save($codigo);
                                }
                                $countValid++;
                            }
                            $this->getOfertaTable()->updateOferta($oferta->id, ['TipoEspecial' => '1']);
                            $mensaje[] = 'Se registraron un total de ' . $countValid . ' Códigos.';
                            $type = 'success';
                        }
                    }
                }
            }
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'oferta' => 'active',
                'oacodigo' => 'active',
                'mensaje' => $mensaje,
                'type' => $type,
                'form' => $form,
            )
        );
    }
}