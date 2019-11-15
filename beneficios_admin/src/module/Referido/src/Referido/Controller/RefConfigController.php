<?php

namespace Referido\Controller;

use EmpresaCliente\Service\Resize;
use Intervention\Image\ImageManager;
use Referido\Form\ConfiguracionRefForm;
use Referido\Model\ConfiguracionReferidos;
use Referido\Model\Filter\ConfiguracionRedFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;

class RefConfigController extends AbstractActionController
{
    #region ObjectTables
    public function getConfiguracionReferidosTable()
    {
        return $this->serviceLocator->get('Referido\Model\Table\ConfiguracionReferidosTable');
    }

    #endregion

    public function indexAction()
    {
        $formManager = $this->serviceLocator->get('FormElementManager');
        $form = $formManager->get('Referido\Form\ConfiguracionRefForm');

        $configuracion = $this->getConfiguracionReferidosTable()->fetchAll();
//var_dump($configuracion);exit;
        $imagen_banner = "";
        $imagen_popup = "";
        $message = "";
        $type = "";
        $imagen_banner_error = "";
        $imagen_popup_error = "";

        foreach ($configuracion as $value) {
            if ($value->Tipo == "puntos") {
                $form->get('repeticion_0' . $value->Campo)->setAttribute('value', $value->Atributo);
            } elseif ($value->Tipo == "correo") {
                $form->get('correo_ref')->setAttribute('value', $value->Atributo);
            } elseif ($value->Tipo == "imagen") {
                if ($value->Campo == 'banner_link') {
                    $form->get('banner_link_ref')->setAttribute('value', $value->Atributo);
                } elseif ($value->Campo == 'banner') {
                    $imagen_banner = $value->Atributo;
                } else {
                    $imagen_popup = $value->Atributo;
                }
            }
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $error_banner = false;
            $error_popup = false;

            $nonFile = $request->getPost()->toArray();
            $file_banner = $this->params()->fromFiles('banner_ref');
            $file_popup = $this->params()->fromFiles('popup_ref');

            $data = array_merge($nonFile, array('banner_ref' => $file_banner['name']), array('popup_ref' => $file_popup['name']));

            $form = new ConfiguracionRefForm();
            $filter = new ConfiguracionRedFilter();

            $form->setInputFilter($filter->getInputFilter());
            $form->setData($data);

            $this->validarImagen($file_banner, $imagen_banner_error, $error_banner);
            $this->validarImagen($file_popup, $imagen_popup_error, $error_popup);

            if ($form->isValid() && !$error_banner && !$error_popup) {
                #region Repeticiones
                $repeticion_01 = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo(1);
                if ($repeticion_01) {
                    $repeticion_01->Campo = 1;
                    $repeticion_01->Atributo = $data['repeticion_01'];
                    $repeticion_01->Tipo = 'puntos';
                    $repeticion_01->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($repeticion_01);
                } else {
                    $config_repeticiones = new ConfiguracionReferidos();
                    $config_repeticiones->Campo = 1;
                    $config_repeticiones->Atributo = $data['repeticion_01'];
                    $config_repeticiones->Tipo = 'puntos';
                    $config_repeticiones->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_repeticiones);
                }

                $repeticion_02 = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo(2);
                if ($repeticion_02) {
                    $repeticion_02->Campo = 2;
                    $repeticion_02->Atributo = $data['repeticion_02'];
                    $repeticion_02->Tipo = 'puntos';
                    $repeticion_02->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($repeticion_02);
                } else {
                    $config_repeticiones = new ConfiguracionReferidos();
                    $config_repeticiones->Campo = 2;
                    $config_repeticiones->Atributo = $data['repeticion_02'];
                    $config_repeticiones->Tipo = 'puntos';
                    $config_repeticiones->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_repeticiones);
                }

                $repeticion_03 = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo(3);
                if ($repeticion_03) {
                    $repeticion_03->Campo = 3;
                    $repeticion_03->Atributo = $data['repeticion_03'];
                    $repeticion_03->Tipo = 'puntos';
                    $repeticion_03->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($repeticion_03);
                } else {
                    $config_repeticiones = new ConfiguracionReferidos();
                    $config_repeticiones->Campo = 3;
                    $config_repeticiones->Atributo = $data['repeticion_03'];
                    $config_repeticiones->Tipo = 'puntos';
                    $config_repeticiones->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_repeticiones);
                }


                $repeticion_04 = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo(4);
                if ($repeticion_04) {
                    $repeticion_04->Campo = 4;
                    $repeticion_04->Atributo = $data['repeticion_04'];
                    $repeticion_04->Tipo = 'puntos';
                    $repeticion_04->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($repeticion_04);
                } else {
                    $config_repeticiones = new ConfiguracionReferidos();
                    $config_repeticiones->Campo = 3;
                    $config_repeticiones->Atributo = $data['repeticion_03'];
                    $config_repeticiones->Tipo = 'puntos';
                    $config_repeticiones->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_repeticiones);
                }
                #endregion

                #region Banners
                if (!empty($file_banner['tmp_name'])) {
                    $manager = new ImageManager(array('driver' => 'imagick'));
                    $img = $manager->make($file_banner['tmp_name']);
                    $path = './public/elements/banner_referidos/';
                    $config = $this->getServiceLocator()->get('Config');

                    $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);
                    $partes = explode(".", $file_banner['name']);
                    $ext = end($partes);
                    $size = 'banners';
                    $resize = new Resize();
                    $resize_bool[$size] = $resize->isResize($img, $config, $size);

                    $guardado = false;
                    try {
                        $resize->rename($path, $img, $ext, $fileName, '');
                        $guardado = true;
                    } catch (\Exception $ex) {
                        echo $ex;
                    }

                    if ($guardado) {
                        $banner = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo('banner');
                        if ($banner) {
                            $banner->Campo = 'banner';
                            $banner->Atributo = $fileName . "." . $ext;
                            $banner->Tipo = 'imagen';
                            $banner->Eliminado = 0;
                            $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($banner);
                        } else {
                            $config_banner = new ConfiguracionReferidos();
                            $config_banner->Campo = 'banner';
                            $config_banner->Atributo = $fileName . "." . $ext;
                            $config_banner->Tipo = 'imagen';
                            $config_banner->Eliminado = 0;
                            $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_banner);
                        }
                        $imagen_banner = $fileName . "." . $ext;
                    }
                }
                #endregion

                #region Popup
                if (!empty($file_popup['tmp_name'])) {
                    $manager = new ImageManager(array('driver' => 'imagick'));
                    $img = $manager->make($file_popup['tmp_name']);
                    $path = './public/elements/banner_referidos/';
                    $config = $this->getServiceLocator()->get('Config');

                    $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);
                    $partes = explode(".", $file_popup['name']);
                    $ext = end($partes);
                    $size = 'popup';
                    $resize = new Resize();
                    $img->resize($config[$size]['width'], $config[$size]['height']);
                    $guardado = false;
                    try {
                        $resize->rename($path, $img, $ext, $fileName, '');
                        $guardado = true;
                    } catch (\Exception $ex) {
                        echo $ex;
                    }

                    if ($guardado) {
                        $popup = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo('popup');
                        if ($popup) {
                            $popup->Campo = 'popup';
                            $popup->Atributo = $fileName . "." . $ext;
                            $popup->Tipo = 'imagen';
                            $popup->Eliminado = 0;
                            $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($popup);
                        } else {
                            $config_popup = new ConfiguracionReferidos();
                            $config_popup->Campo = 'popup';
                            $config_popup->Atributo = $fileName . "." . $ext;
                            $config_popup->Tipo = 'imagen';
                            $config_popup->Eliminado = 0;
                            $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_popup);
                        }
                        $imagen_popup = $fileName . "." . $ext;
                    }
                }
                #endregion

                $banner_link_ref = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo('banner_link');
                if ($banner_link_ref) {
                    $banner_link_ref->Campo = 'banner_link';
                    $banner_link_ref->Atributo = $data['banner_link_ref'];
                    $banner_link_ref->Tipo = 'imagen';
                    $banner_link_ref->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($banner_link_ref);
                } else {
                    $config_banner_link = new ConfiguracionReferidos();
                    $config_banner_link->Campo = 'banner_link';
                    $config_banner_link->Atributo = $data['banner_link_ref'];
                    $config_banner_link->Tipo = 'imagen';
                    $config_banner_link->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_banner_link);
                }

                $correo_ref = $this->getConfiguracionReferidosTable()->getConfiguracionReferidosByCampo('correo_admin');
                if ($correo_ref) {
                    $correo_ref->Campo = 'correo_admin';
                    $correo_ref->Atributo = $data['correo_ref'];
                    $correo_ref->Tipo = 'correo';
                    $correo_ref->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($correo_ref);
                } else {
                    $config_correo = new ConfiguracionReferidos();
                    $config_correo->Campo = 'correo_admin';
                    $config_correo->Atributo = $data['correo_ref'];
                    $config_correo->Tipo = 'correo';
                    $config_correo->Eliminado = 0;
                    $this->getConfiguracionReferidosTable()->saveConfiguracionReferidos($config_correo);
                }

                $type = 'success';
                $message = 'Configuraciones Guardadas Correctamente';
            } else {
                $type = 'error';
                $message = 'Ocurrio un error al procesar los datos';
            }
        }

        return new ViewModel(
            array(
                'referido' => 'active',
                'refconfig' => 'active',
                'form' => $form,
                'imagen_banner' => $imagen_banner,
                'imagen_popup' => $imagen_popup,
                'message' => $message,
                'imagen_banner_error' => $imagen_banner_error,
                'imagen_popup_error' => $imagen_popup_error,
                'type' => $type,
            )
        );
    }

    private function validarImagen($File, &$imagen_message, &$error)
    {
        if (!empty($File['tmp_name'])) {
            //Valida el tamaño del archivo
            $sizeValidator = new Size(array('max' => 2097152)); //tamaño maximo en bytes
            //Valida la extension del archivo
            $extensionValidator = new Extension(array('extension' => array('png', 'jpg', 'jpeg')), true);

            if (!$sizeValidator->isValid($File) || !$extensionValidator->isValid($File)) {
                $imagen_message = 'El archivo subido no es una imagen';
                $error = true;
            } else {
                $error = false;
            }
        } else {
            $imagen_message = "";
            $error = false;
        }
    }
}