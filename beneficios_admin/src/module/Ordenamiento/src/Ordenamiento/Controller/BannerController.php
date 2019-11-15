<?php

namespace Ordenamiento\Controller;

use EmpresaCliente\Service\Resize;
use Intervention\Image\ImageManager;
use Ordenamiento\Form\GaleryForm;
use Ordenamiento\Model\BannersCampanias;
use Ordenamiento\Model\BannersCategoria;
use Ordenamiento\Model\BannersTienda;
use Ordenamiento\Model\Galeria;
use Ordenamiento\Form\BannerForm;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\Validator\File\UploadFile;
use Zend\View\Model\ViewModel;

class BannerController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;

    #region ObjectTables
    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaTable');
    }

    public function getBannersTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannerTable');
    }

    public function getBannersCampaniaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannersCampaniasTable');
    }

    public function getBannersCategoriaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannersCategoriaTable');
    }

    public function getBannersTiendaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannersTiendaTable');
    }

    public function getGaleriasTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\GaleriaTable');
    }

    public function getLayoutCategoriaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutCategoriaTable');
    }

    public function getLayoutCampaniaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutCampaniaTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    #endregion

    public function extraerCategoria()
    {
        $cbxcategoria = array();
        try {
            $datos = $this->getCategoriaTable()->fetchAll();
            foreach ($datos as $dato) {
                $cbxcategoria[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $cbxcategoria;
        }
        return $cbxcategoria;
    }

    public function extraerCampania()
    {
        $cbxcamania = array();
        try {
            $datos = $this->getCampaniaTable()->fetchAll();
            foreach ($datos as $dato) {
                $cbxcamania[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $cbxcamania;
        }
        return $cbxcamania;
    }

    public function extraerEmpresa()
    {
        $empresas = array();
        try {
            $dataEmpresas = $this->getEmpresaTable()->getEmpresaCli();
            $empresas["all"] = "Listar Todos";
            foreach ($dataEmpresas as $e) {
                $empresas[$e->id] = $e->NombreComercial . " (" . $e->RazonSocial . ") - " . $e->Ruc;
            }
        } catch (\Exception $ex) {
            $empresas = array();
        }
        return $empresas;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function bannerAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $alert = null;
        $msg = null;
        $galeria = null;
        $lista = null;
        $banners = array();
        $nombre_empresa = null;

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $this->extraerEmpresa()[$empresa_value];
            $form = new BannerForm($this->extraerCategoria(), $this->extraerCampania(), $empresa_value, $tipo_usuario);
        } else {
            $form = new BannerForm($this->extraerCategoria(), $this->extraerCampania(), $this->extraerEmpresa());
        }

        $formgalery = new GaleryForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $files = $this->getRequest()->getFiles()->toArray();
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $files
            );

            $empresa_id = $post["empresa"];
            if ($post["tipo"] == 1) {
                $valor = $post["BNF_Campanias_id"];
            } elseif ($post["tipo"] == 0) {
                $valor = $post["BNF_Categoria_id"];
            } elseif ($post["tipo"] == 3) {
                $valor = 9;
            } elseif ($post["tipo"] == 4) {
                $valor = 10;
            } else {
                $valor = 3;
            }

            if (!empty($valor)) {
                if ($valor == 1 and $post["tipo"] == 0) {
                    $banners[1] = $post["Banner01"];
                    $banners[2] = $post["Banner02"];
                    $banners[3] = $post["Banner03"];
                    $banners[4] = $post["Banner04"];

                    $links[1] = $post["Banner01Url"];
                    $links[2] = $post["Banner02Url"];
                    $links[3] = $post["Banner03Url"];
                    $links[4] = $post["Banner04Url"];
                } elseif ($post["tipo"] == 3) {
                    $banners[2] = $post["Banner02"];
                    $banners[3] = $post["Banner03"];
                    $banners[4] = $post["Banner04"];
                    $banners[5] = $post["Banner05"];
                    $banners[6] = $post["Banner06"];

                    $links[2] = $post["Banner02Url"];
                    $links[3] = $post["Banner03Url"];
                    $links[4] = $post["Banner04Url"];
                    $links[5] = $post["Banner05Url"];
                    $links[6] = $post["Banner06Url"];
                } elseif ($post["tipo"] == 4) {
                    $banners[2] = $post["Banner02"];
                    $banners[3] = $post["Banner03"];
                    $banners[4] = $post["Banner04"];
                    $banners[5] = $post["Banner05"];
                    $banners[6] = $post["Banner06"];

                    $links[2] = $post["Banner02Url"];
                    $links[3] = $post["Banner03Url"];
                    $links[4] = $post["Banner04Url"];
                    $links[5] = $post["Banner05Url"];
                    $links[6] = $post["Banner06Url"];
                } else {
                    $banners[2] = $post["Banner02"];
                    $banners[3] = $post["Banner03"];
                    $banners[4] = $post["Banner04"];
                    $banners[5] = $post["Banner05"];

                    $links[2] = $post["Banner02Url"];
                    $links[3] = $post["Banner03Url"];
                    $links[4] = $post["Banner04Url"];
                    $links[5] = $post["Banner05Url"];
                }

                foreach ($banners as $key => $value) {
                    if ($this->verificarImagen($value)[0]) {
                        $manager = new ImageManager(array('driver' => 'imagick'));
                        $img = $manager->make($value['tmp_name']);
                        $path = './public/elements/banners/';
                        $config = $this->getServiceLocator()->get('Config');

                        $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);
                        $partes = explode(".", $value['name']);
                        $ext = end($partes);
                        $size = 'banners';
                        if ($key == 1) {
                            $size = 'imagen_principal';
                        }

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
                            $banner = $this->getBannersTable()->getBanner($key);
                            if ($post["tipo"] == 1) {
                                $result = $this->getBannersCampaniaTable()
                                    ->getBannerCampaniaExist($banner->id, $valor, $empresa_id);
                                if ($result > 0) {
                                    $bannerdata = $this->getBannersCampaniaTable()
                                        ->getBannerCampaniabyBanner($banner->id, $valor, $empresa_id);
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->BNF_Campanias_id = $valor;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                } else {
                                    $bannerdata = new BannersCampanias();
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->BNF_Campanias_id = $valor;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                }
                                $this->getBannersCampaniaTable()->saveBannerCampania($bannerdata);
                            } elseif ($post["tipo"] == 0 || $post["tipo"] == 3) {
                                $result = $this->getBannersCategoriaTable()
                                    ->getBannerCategoriaExist($banner->id, $valor, $empresa_id);
                                if ($result) {
                                    $bannerdata = $this->getBannersCategoriaTable()
                                        ->getBannerCategoriabyBanner($banner->id, $valor, $empresa_id);
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->BNF_Categoria_id = $valor;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                } else {
                                    $bannerdata = new BannersCategoria();
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->BNF_Categoria_id = $valor;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                }
                                $this->getBannersCategoriaTable()->saveBannerCategoria($bannerdata);
                            } elseif ($post["tipo"] == 0 || $post["tipo"] == 4) {
                                $result = $this->getBannersCategoriaTable()
                                    ->getBannerCategoriaExist($banner->id, $valor, $empresa_id);
                                if ($result) {
                                    $bannerdata = $this->getBannersCategoriaTable()
                                        ->getBannerCategoriabyBanner($banner->id, $valor, $empresa_id);
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->BNF_Categoria_id = $valor;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                } else {
                                    $bannerdata = new BannersCategoria();
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->BNF_Categoria_id = $valor;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                }
                                $this->getBannersCategoriaTable()->saveBannerCategoria($bannerdata);
                            } else {
                                $result = $this->getBannersTiendaTable()
                                    ->getBannerTiendaExist($banner->id, $empresa_id);
                                if ($result > 0) {
                                    $bannerdata = $this->getBannersTiendaTable()
                                        ->getBannerTiendabyBanner($banner->id, $empresa_id);
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                } else {
                                    $bannerdata = new BannersTienda();
                                    $bannerdata->Eliminado = "0";
                                    $bannerdata->Imagen = $fileName . '.' . $ext;
                                    $bannerdata->BNF_Banners_id = $banner->id;
                                    $bannerdata->Url = $links[$key];
                                    $bannerdata->Posicion = $key;
                                    $bannerdata->BNF_Empresa_id = $empresa_id;
                                }
                                $this->getBannersTiendaTable()->saveBannerTienda($bannerdata);
                            }

                            $alert = 'success';
                            $msg[] = $banner->Nombre . ' Registradas Correctamente';
                        }
                    }
                }

                if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                    $nombre_empresa = $this->extraerEmpresa()[$empresa_value];
                    $form = new BannerForm(
                        $this->extraerCategoria(),
                        $this->extraerCampania(),
                        $empresa_value,
                        $tipo_usuario
                    );
                } else {
                    $form =
                        new BannerForm($this->extraerCategoria(), $this->extraerCampania(), $this->extraerEmpresa());
                }
                $formgalery = new GaleryForm();

            } else {
                $alert = 'error';
                if ($post["tipo"]) {
                    $msg[] = "No hay una Campaña seleccionada.";
                } else {
                    $msg[] = "No hay una Categoria seleccionada.";
                }
            }
        }

        $galeriaImagenes = $this->getGaleriasTable()->getGaleriaAll($empresa_value);

        return new ViewModel(
            array(
                'ordenamiento' => 'active',
                'orbanner' => 'active',
                'datos' => $galeriaImagenes,
                'alert' => $alert,
                'msg' => $msg,
                'form' => $form,
                'formgalery' => $formgalery,
                'nombre_empresa' => $nombre_empresa,
            )
        );
    }

    public function addgaleryAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $id = 0;
        if ($request->isPost()) {
            $files = $this->getRequest()->getFiles()->toArray();
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $files
            );
            $galeria = $post["Galeria"];
            $linkgaleria = $post["GaleriaUrl"];
            $empresa_id = $post["empresa_g"];
            if ($this->verificarImagen($galeria)[0]) {
                $manager = new ImageManager(array('driver' => 'imagick'));
                $img = $manager->make($galeria['tmp_name']);
                $config = $this->getServiceLocator()->get('Config');
                $fileName = date("Y-m-d_H-i-s") . '-' . rand(1000, 9999);
                $partes = explode(".", $galeria['name']);
                $ext = end($partes);
                $path = './public/elements/galeria/';

                $resize = new Resize();

                $resize_bool['galeria'] = $resize->isResize($img, $config, 'galeria');

                $proporcion = $img->getWidth() / $img->getHeight();

                $guardado = false;
                /*if ($proporcion < 2.27) {
                    try {
                        $resize->resizeHeight($path, $img, $ext, $fileName, $config, 'galeria', '', $resize_bool);
                        $guardado = true;
                    } catch (\Exception $e) {
                        echo $e;
                    }
                } else {
                    try {
                        $resize->resizeWidth($path, $img, $ext, $fileName, $config, 'galeria', '', $resize_bool);
                        $guardado = true;
                    } catch (\Exception $e) {
                        echo $e;
                    }
                }*/
                try {
                    $resize->rename($path, $img, $ext, $fileName, '');
                    $guardado = true;
                } catch (\Exception $ex) {
                    echo $ex;
                }
                if ($guardado) {
                    $galeriaImagen = new Galeria();
                    $galeriaImagen->Imagen = $fileName . '.' . $ext;
                    $galeriaImagen->Url = $linkgaleria;
                    $galeriaImagen->Eliminado = '0';
                    if ($empresa_id != '') {
                        $galeriaImagen->BNF_Empresa_id = $empresa_id;
                    }
                    $id = $this->getGaleriasTable()->saveGaleria($galeriaImagen);
                }

                $response->setContent(
                    Json::encode(
                        array('response' => true, 'value' => $id, 'name' => $fileName . '.' . $ext)
                    )
                );
            } else {
                $response->setContent(
                    Json::encode(array('response' => false, 'message' => $this->verificarImagen($galeria)[1]))
                );
            }
        }
        return $response;
    }

    public function getBannersCategoriaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['val'];
            $empresa_id = (int)$post_data['emp'];
            if ($id != 0) {
                try {
                    $bannersCategoria = $this->getBannersCategoriaTable()->getBannersbyCategoria($id, $empresa_id);
                } catch (\Exception $ex) {
                    $bannersCategoria = array();
                }

                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'value' => $bannersCategoria
                        )
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false)));
            }
        }
        return $response;
    }

    public function getBannersCampaniaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['val'];
            $empresa_id = (int)$post_data['emp'];
            if ($id != 0) {
                try {
                    $bannersCategoria = $this->getBannersCampaniaTable()->getBannersbyCampania($id, $empresa_id);
                } catch (\Exception $ex) {
                    $bannersCategoria = array();
                }

                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'value' => $bannersCategoria
                        )
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false)));
            }
        }
        return $response;
    }

    public function getBannersTiendaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $empresa_id = (int)$post_data['emp'];
            try {
                $bannersTienda = $this->getBannersTiendaTable()->getBannerTiendaAll($empresa_id);
            } catch (\Exception $ex) {
                $bannersTienda = array();
            }

            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $bannersTienda
                    )
                )
            );
        }
        return $response;
    }

    public function verificarImagen($imagen)
    {
        $config = $this->getServiceLocator()->get('Config');
        $upload = new UploadFile();
        $size = new Size(array('max' => $config['size_file_upload']));
        $extension = new Extension(array('jpg', 'png'));

        if ($upload->isValid($imagen)) {
            if ($size->isValid($imagen)) {
                if (!$extension->isValid($imagen)) {
                    return [false, "La extensión es incorrecta"];
                } else {
                    return [true, null];
                }
            } else {
                return [false, "El tamaño de la imagen no debe de superar los " .
                    round(($config['size_file_upload'] / 1024) / 1024, 2) . "MB"];
            }
        } else {
            return [false, "El archivo no es una imagen"];
        }
    }

    public function deleteGaleriaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $val = $request->getPost('val');
        $id = $request->getPost('id');

        if ($this->getGaleriasTable()->deleteGaleria($id, $val)) {
            $response->setContent(Json::encode(array('response' => true, 'message' => 'La Imagen fue eliminado.')));
        } else {
            $response->setContent(Json::encode(array('response' => false, 'message' => 'La Imagen no existe.')));
        }

        return $response;
    }

    public function editlinkAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $val = $request->getPost('val');
        $id = $request->getPost('id');
        $tipoban = $request->getPost('tipo');

        if ($tipoban == 1) {
            if ($this->getBannersCampaniaTable()->editLinkBannerCampania($id, $val)) {
                $response->setContent(
                    Json::encode(
                        array('response' => true, 'message' => 'El link fue editado.')
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'El link no fue editado.')));
            }
        } else {
            if ($this->getBannersCategoriaTable()->editLinkBannerCategoria($id, $val)) {
                $response->setContent(
                    Json::encode(
                        array('response' => true, 'message' => 'El link fue editado.')
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'El link no fue editado.')));
            }
        }
        return $response;
    }

    public function editlinkgaleryAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $val = $request->getPost('val');
        $id = $request->getPost('id');

        if ($this->getGaleriasTable()->editlinkGaleria($id, $val)) {
            $response->setContent(
                Json::encode(
                    array('response' => true, 'message' => 'El link fue editado.')
                )
            );
        } else {
            $response->setContent(Json::encode(array('response' => false, 'message' => 'El link no fue editado.')));
        }
        return $response;
    }

    public function deleteBannerAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $val = $request->getPost('val');
        $id = $request->getPost('id');
        $ban = $request->getPost('ban');
        $tipoban = $request->getPost('tipo');

        if ($tipoban == 1) {
            if ($this->getBannersCampaniaTable()->deleteBannerCampania($id, $val, $ban)) {
                $response->setContent(
                    Json::encode(
                        array('response' => true, 'message' => 'El Banner fue desactivado.')
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'El Banner no existe.')));
            }
        } else {
            if ($this->getBannersCategoriaTable()->deleteBannerCategoria($id, $val, $ban)) {
                $response->setContent(
                    Json::encode(
                        array('response' => true, 'message' => 'El Banner fue desactivado.')
                    )
                );
            } else {
                $response->setContent(Json::encode(array('response' => false, 'message' => 'El Banner no existe.')));
            }
        }
        return $response;
    }

    public function getGaleriaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $empresa_id = (int)$post_data['val'];
            try {
                $galeriaImagenes = $this->getGaleriasTable()->getGaleriaAll($empresa_id);
            } catch (\Exception $ex) {
                $galeriaImagenes = array();
            }

            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $galeriaImagenes
                    )
                )
            );
        }
        return $response;
    }
}
