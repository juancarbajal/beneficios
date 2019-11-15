<?php

namespace Auth\Controller;

use Auth\Form\Filter\LoginFilter;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\Adapter\DbTable;
use Zend\View\Model\ViewModel;
use Auth\Form\LoginForm;

class AuthController extends AbstractActionController
{
    const TIPO_PROVEEDOR = 'proveedor';
    const TIPO_CLIENTE = 'cliente';
    const TIPO_REFERIDO = 'verisure';

    public function indexAction()
    {
        $url = '/paquete';
        $authService = $this->getServiceLocator()->get('auth_service');
        if (!$authService->hasIdentity()) {
            // if not log in, redirect to login page
            return $this->redirect()->toUrl('/login');
        }
        $tipoUsuario = $authService->getStorage()->read()->TipoUsuario;
        if ($tipoUsuario == "super") {
            $url = '/paquete';
        } elseif ($tipoUsuario == "admin") {
            $url = '/paquete';
        } elseif ($tipoUsuario == "asesor") {
            $url = '/logout';
        } elseif ($tipoUsuario == "demanda") {
            $url = '/empresa';
        } elseif ($tipoUsuario == "oferta") {
            $url = '/oferta';
        } elseif ($tipoUsuario == "proveedor") {
            $url = '/cupon';
        } elseif ($tipoUsuario == "cliente") {
            $url = '/cliente';
        } elseif ($tipoUsuario == "verisure") {
            $url = '/cliente-landing';
        }
        return $this->redirect()->toUrl($url);
    }

    public function loginAction()
    {
        $msg = null;
        $authService = $this->getServiceLocator()->get('auth_service');

        if ($authService->hasIdentity()) {
            // if not log in, redirect to login page
            return $this->redirect()->toUrl('/');
        }

        $form = new LoginForm();

        if ($this->getRequest()->isPost()) {
            $filter = new LoginFilter();
            $form->setInputFilter($filter->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if (!$form->isValid()) {
                // not valid form
                return new ViewModel(
                    array(
                        'title' => 'Log In',
                        'form' => $form
                    )
                );
            }

            $user = $this->serviceLocator->get('Usuario\Model\Table\UsuarioTable');
            $getEmpresa = $this->serviceLocator->get('Empresa\Model\EmpresaTable');
            $loginData = $form->getData();
            $guest = $user->getUsuarioPorCorreo($loginData['Correo']);
            $verificado = false;
            if ($guest) {
                $bcrypt = new Bcrypt();
                $securePass = $guest->Contrasenia;
                $password = $loginData['Contrasenia'];

                if ($bcrypt->verify($password, $securePass)) {
                    $verificado = true;
                } else {

                    if (md5($loginData['Contrasenia']) == $securePass) {
                        $guest->Contrasenia = $loginData['Contrasenia'];
                        $user->saveUsuario($guest);
                        $verificado = true;
                    }
                }

                if ($verificado) {
                    // set id as identifier in session
                    $userId = $guest->id;
                    $user->saveLastLogin($userId);

                    $tipoUsuario_id = $guest->BNF_TipoUsuario_id;
                    $tipoUsuarioTable = $this->serviceLocator->get('Usuario\Model\TipoUsuarioTable');
                    $tipoUsuario = $tipoUsuarioTable->getTipoUsuario($tipoUsuario_id)->Descripcion;
                    $empresa = $getEmpresa->getEmpresa($guest->BNF_Empresa_id);
                    $data_user = array();
                    $data_user["id"] = $guest->id;
                    $data_user["Correo"] = $guest->Correo;
                    $data_user["Nombres"] = $guest->Nombres;
                    $data_user["Apellidos"] = $guest->Apellidos;
                    $data_user["BNF_TipoUsuario_id"] = $guest->BNF_TipoUsuario_id;
                    $data_user["BNF_Empresa_id"] = $guest->BNF_Empresa_id;
                    $data_user["TipoUsuario"] = $tipoUsuario;
                    $data_user["Logo"] = isset($empresa->Logo_sitio) ? $empresa->Logo_sitio : null;
                    $data_user["Color"] = isset($empresa->Color) ? $empresa->Color : null;

                    $empresaTable = $this->serviceLocator->get('Empresa\Model\EmpresaTable');

                    if ($tipoUsuario == $this::TIPO_PROVEEDOR) {
                        $estadoEmpresa = $empresaTable->getEmpresaProvActiva($guest->BNF_Empresa_id);
                        if ($estadoEmpresa) {
                            $data_user = (object)$data_user;
                            $authService->getStorage()
                                ->write($data_user);
                            return $this->redirect()->toUrl('/cupon');
                        } else {
                            $msg[] = 'No tiene permiso para ingresar al Sistema.';
                        }
                    } elseif ($tipoUsuario == $this::TIPO_CLIENTE) {
                        $estadoEmpresa = $empresaTable->getEmpresaCliActiva($guest->BNF_Empresa_id);
                        if ($estadoEmpresa) {
                            $data_user = (object)$data_user;
                            $authService->getStorage()
                                ->write($data_user);
                            return $this->redirect()->toUrl('/cliente');
                        } else {
                            $msg[] = 'No tiene permiso para ingresar al Sistema.';
                        }
                    } elseif ($tipoUsuario == $this::TIPO_REFERIDO) {
                        $estadoEmpresa = $empresaTable->getEmpresaCliActiva($guest->BNF_Empresa_id);
                        if ($estadoEmpresa) {
                            $data_user = (object)$data_user;
                            $authService->getStorage()
                                ->write($data_user);
                            return $this->redirect()->toUrl('/cliente-landing');
                        } else {
                            $msg[] = 'No tiene permiso para ingresar al Sistema.';
                        }
                    } else {
                        $data_user = (object)$data_user;
                        $authService->getStorage()
                            ->write($data_user);
                        $url = '/';
                        if ($tipoUsuario == "super") {
                            $url = '/paquete';
                        } elseif ($tipoUsuario == "admin") {
                            $url = '/paquete';
                        } elseif ($tipoUsuario == "asesor") {
                            $url = '/logout';
                        } elseif ($tipoUsuario == "demanda") {
                            $url = '/empresa';
                        } elseif ($tipoUsuario == "oferta") {
                            $url = '/oferta';
                        }
                        return $this->redirect()->toUrl($url);
                    }
                } else {
                    $msg[] = 'Correo o Contraseña Incorrecta.';
                }
            } else {
                $msg[] = 'No tiene permiso para ingresar al Sistema.';
            }
        }

        return new ViewModel(
            array(
                'title' => 'Log In',
                'form' => $form,
                'msg' => $msg
            )
        );
    }

    public function logoutAction()
    {
        $authService = $this->getServiceLocator()->get('auth_service');
        $authService->clearIdentity();
        return $this->redirect()->toUrl('/login');
    }
}
