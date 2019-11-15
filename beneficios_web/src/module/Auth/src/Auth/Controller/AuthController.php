<?php

namespace Auth\Controller;

use Application\Form\BaseForm;
use Auth\Form\LoginForm;
use Perfil\Services\Puntos;
use Premios\Services\Premios;
use Zend\Authentication\Adapter\DbTable;
use Zend\Debug\Debug;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;

class AuthController extends AbstractActionController
{
    protected $clienteTable;
    protected $empresaTable;
    protected $empresaClienteTable;
    protected $categoriaTable;

    const PAIS_DEFAULT = 1;

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\ClienteTable');
    }

    public function getEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\EmpresaClienteClienteTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
    }

    public function getSubDominio()
    {
        $config = $this->getServiceLocator()->get('Config');
        $URLactual = $_SERVER['SERVER_NAME'];
        $subDominio = explode($config['domain'], $URLactual)[0];
        $subDominio = str_replace("www.", "", $subDominio);
        $subDominio = str_replace(".", "", $subDominio);

        return $subDominio;
    }

    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $authService = $this->getServiceLocator()->get('auth_service');
        if (!$authService->hasIdentity()) {
            // if not log in, redirect to login page
            $subDominio = $this->getSubDominio();
            if (in_array($subDominio, $config['empresas_especiales'])) {
                $this->loginTebca($subDominio);
            } else {
                return $this->redirect()->toUrl('/login');
            }
        }

        return $this->redirect()->toUrl('/home');
    }

    public function loginAction()
    {
        $authService = $this->getServiceLocator()->get('auth_service');

        if ($authService->hasIdentity()) {
            return $this->redirect()->toUrl('/');
        }

        $form = new LoginForm();
        $loginMsg = array();
        $subDominio = $this->getSubDominio();
        $config = $this->getServiceLocator()->get('Config');

        if ($this->getRequest()->isPost()) {
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


            $clienteTable = $this->serviceLocator->get('Auth\Model\Table\ClienteTable');
            $clienteCorreoTable = $this->serviceLocator->get('Auth\Model\Table\ClienteCorreoTable');
            $preguntasTable = $this->serviceLocator->get('Application\Model\Table\PreguntasTable');
            $empresaTable = $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
            $segmentoTable = $this->serviceLocator->get('Auth\Model\Table\EmpresaSegmentoTable');
            $subgrupoTable = $this->serviceLocator->get('Auth\Model\Table\EmpresaSubgrupoTable');
            $asignacionTable = $this->serviceLocator->get('Application\Model\Table\AsignacionTable');
            $ofertaTable = $this->serviceLocator->get('Application\Model\Table\OfertaPuntosTable');
            $asignacionPremiosTable = $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosTable');
            $ofertaPremiosTable = $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');


            $loginData = $form->getData();

            $cliente = $clienteTable->getClienteNumeroDocumeto($loginData['dni']);

            $empresaClienteCliente= $this->getEmpresaClienteTable()->searchEmpresaCliente($loginData['empresa_id'], $cliente->id);


            $clienteCorreo = $clienteCorreoTable->getCorreos($cliente->id);
            $empresa = $empresaTable->getEmpresa($loginData['empresa_id'], $cliente->id);
            $segmento = $segmentoTable->getEmpresasSegmento($loginData['empresa_id'], $cliente->id);
            $subgrupo = $subgrupoTable->getEmpresasSubgrupo($loginData['empresa_id'], $cliente->id);
            $puntos = $asignacionTable->getExistAssignedForUsuariosAndEmpresa($loginData['empresa_id'], $cliente->id);

            $dataAsignacion = $asignacionTable->getAsignacionesCliente($cliente->id);
            $segmentos = array();
            if (is_array($dataAsignacion) || is_object($dataAsignacion)) {
                foreach ($dataAsignacion as $value) {
                    $segmentos[] = $value->BNF2_Segmento_id;
                }
            }

            $all_asignacion = $asignacionTable->getAsignacionForCliente($cliente->id);
            $have_asignacion = false;
            $totalAsig = 0;
            foreach ($all_asignacion as $value) {
                $totalAsig++;
            }

            if ($totalAsig > 0) {
                $have_asignacion = true;
            }

            $exist_ofertas = false;
            if($have_asignacion) {
                $dataOfertas = $ofertaTable->getOrdenamientoOfertas(0, $loginData['empresa_id'], 9, null, $segmentos, -1, 0, null);
                $exist_ofertas = false;
                $count = 0;
                if (count($dataOfertas) > 0) {
                    foreach ($dataOfertas as $item) {
                        $count++;
                    }
                }

                if ($count > 0) {
                    $exist_ofertas = true;
                }
            }

            $premios = $asignacionPremiosTable->getExistAssignedForUsuariosAndEmpresa($loginData['empresa_id'], $cliente->id);
            $dataAsignacionPremios = $asignacionPremiosTable->getAsignacionesCliente($cliente->id);
            $segmentosPremios = array();
            if (is_array($dataAsignacionPremios) || is_object($dataAsignacionPremios)) {
                foreach ($dataAsignacionPremios as $value) {
                    $segmentosPremios[] = $value->BNF3_Segmento_id;
                }
            }

            $all_asignacion_premios = $asignacionPremiosTable->getAsignacionForCliente($cliente->id);
            $have_asignacion_premios = false;
            $totalAsig = 0;
            foreach ($all_asignacion_premios as $value) {
                $totalAsig++;
            }

            if ($totalAsig > 0) {
                $have_asignacion_premios = true;
            }

            $exist_ofertas_premios = false;
            if($have_asignacion_premios) {
                $dataOfertasPremios = $ofertaPremiosTable->getOrdenamientoOfertas(0, $loginData['empresa_id'], 10, null, $segmentosPremios, -1, 0, null);
                $exist_ofertas_premios = false;
                $count = 0;
                if (count($dataOfertasPremios) > 0) {
                    foreach ($dataOfertasPremios as $item) {
                        $count++;
                    }
                }

                if ($count > 0) {
                    $exist_ofertas_premios = true;
                }
            }

            $authService = $this->getServiceLocator()->get('auth_service');
            $preguntas = $preguntasTable->getPreguntas($cliente->id);

            $clienteTable->updateConection(
                array('UltimaConexion' => date("Y-m-d H:i:s")),
                $cliente->id
            );

            if ($loginData['email'] == '') {
                if ($clienteCorreo == false) {
                    $correoCliente = '';
                } else {
                    $mails = array();
                    foreach ($clienteCorreo as $mail) {
                        array_push($mails, $mail);
                    }
                    $correoCliente = $mails[0]->Correo;
                }
            } else {
                $correoCliente = $loginData['email'];
                $clienteCorreoTable->saveCorreo($cliente->id, $correoCliente);
            }

            if ($cliente != false) {
                $servicePuntos = new Puntos($this->serviceLocator);
                $servicePremios = new Premios($this->serviceLocator);
                $cant_puntos = $servicePuntos->getTotalPuntos($cliente->id);
                $cant_premios = $servicePremios->getTotalPremios($cliente->id);

                $data_user = array(
                    'id' => $cliente->id,
                    'NumeroDocumento' => $cliente->NumeroDocumento,
                    'Nombre' => $preguntas->Pregunta01,
                    'Apellido' => $preguntas->Pregunta02,
                    'Tipo' => 1,
                    'Empresa' => $loginData['empresa_id'],
                    'segmento' => (int)$segmento->BNF_Segmento_id,
                    'subgrupo' => (!empty($subgrupo->id)) ? $subgrupo->id : 0,
                    'logo' => $empresa->Logo,
                    'ubigeo' => 14,
                    'email' => $correoCliente,
                    'Color' => $empresa->Color_menu,
                    'flaLogoBeneficio' => $empresa->checkboxLogoBeneficio,
                    'flagcheckboxLogo' => $empresa->checkboxLogo,
                    'flagcheckboxMoney' => $empresa->checkboxMoney,
                    'flagcheckboxTotalPuntos' => $empresa->checkboxTotalPuntos,
                    'Color_hover' => $empresa->Color_hover,
                    'flagsupervisor'=>$empresaClienteCliente->Beneficiario,

                    'exist_puntos' => $puntos,
                    'exist_ofertas' => $exist_ofertas,
                    'have_asignacion' => $have_asignacion,
                    'puntos' => $cant_puntos,
                    'segmentos_puntos' => $segmentos,
                    'modal_puntos' => ($cant_puntos > 0) ?true :false,
                    'exist_premios' => $premios,
                    'exist_ofertas_premios' => $exist_ofertas_premios,
                    'have_asignacion_premios' => $have_asignacion_premios,
                    'premios' => $cant_premios,
                    'segmentos_premios' => $segmentosPremios,
                    'modal_premios' => ($cant_premios > 0) ?true :false,
                    'domain_sullana'=>($subDominio==$config['domain_sullana'])?true:false,
                    'flyout_premios' => false,
                );

                $authService->getStorage()->write($data_user);

                $session = new SessionContainer('url');
                $data_url = $session->offsetGet('storage');

                return $this->redirect()->toUrl(($data_url['url'] != '') ? $data_url['url'] : '/home');
            } else {
                $loginMsg = 'Error';
            }
        }
        $this->layout()->damoain_sullana = ($subDominio==$config['domain_sullana'])?true:false;
        return new ViewModel(
            array(
                'title' => 'Log In',
                'form' => $form,
                'loginMsg' => $loginMsg
            )
        );
    }

    public function loginTebca($subdominio)
    {
        $empresaTable = $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');

            $empresa = $empresaTable->getEmpresaSubDominio($subdominio);
            if ($empresa) {
                $authService = $this->getServiceLocator()->get('auth_service');

                $data_user = array(
                    'Tipo' => 1,
                    'Empresa' => $empresa->id,
                    'logo' => $empresa->Logo,
                    'SubDominio' => $empresa->SubDominio,
                    'ubigeo' => 14,
                    'Color' => $empresa->Color_menu,
                    'Color_hover' => $empresa->Color_hover,
                    'exist_puntos' => false,
                    'exist_ofertas' => false,
                    'have_asignacion' => false,
                    'puntos' => 0,
                    'segmentos_puntos' => [],
                    'modal_puntos' => false,
                    'exist_premios' => false,
                    'exist_ofertas_premios' => false,
                    'have_asignacion_premios' => false,
                    'premios' => 0,
                    'flagcheckboxLogo' => true,
                    'flaLogoBeneficio' => true,
                    'segmentos_premios' => [],
                    'modal_premios' => false,
                    'flyout_premios' => false,
                );

                $authService->getStorage()->write($data_user);
                return $this->redirect()->toUrl('/home');
            }

    }

    public function logoutAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $authService = $this->getServiceLocator()->get('auth_service');
        $authService->clearIdentity();
        $urlService = $this->getServiceLocator()->get('url_service');
        $urlService->clearIdentity();
        $subDominio = $this->getSubDominio();
        session_regenerate_id();
        if (in_array($subDominio, $config['empresas_especiales'])) {
            $this->loginTebca($subDominio);
        } else {
            return $this->redirect()->toUrl('/login');
        }
    }

    public function validateAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $data_response = array('response' => false);
        $form = new BaseForm();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $form->setInputFilter($form->getInputFilter());
            $form->setData($post_data);

            if ($form->isValid()) {
                if ($this->getClienteTable()->getDocumento($post_data['dni']) > 0) {
                    if ($this->getClienteTable()->verifyCliente($post_data['dni'], $post_data['empresa_id']) > 0) {
                        $data_response['response'] = true;
                    }
                }
            }
        }

        $data_response['csrf'] = $form->get('csrf')->getValue();

        $response->setContent(
            Json::encode(
                $data_response
            )
        );

        return $response;
    }

    public function verifyExistAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $data_response = array('response' => false);
        $form = new BaseForm();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $form->setInputFilter($form->getInputFilter());
            $form->setData($post_data);

            if ($form->isValid()) {
                if ($this->getClienteTable()->getDocumento($post_data['dni']) > 0) {
                    $count = $this->getEmpresaClienteTable()->getTotalEmpresasCliente($post_data['dni']);
                    $resultados = $this->getEmpresaClienteTable()->getEmpresasClientebyDoc($post_data['dni']);

                    $lista = array();
                    foreach ($resultados as $dato) {
                        if ($this->getEmpresaTable()->getEmpresa($dato->BNF_Empresa_id)) {
                            $empresa = $this->getEmpresaTable()->getEmpresa($dato->BNF_Empresa_id);
                            $lista[$empresa->id] = $empresa->NombreComercial;
                        }
                    }
                    $manager = new SessionManager();
                    $storage = $manager->getStorage();
                    $storage->clear('Zend_Validator_Csrf_salt_csrf');

                    $form = new BaseForm();

                    $data_response['response'] = true;
                    $data_response['total'] = $count;
                    $data_response['empresas'] = $lista;
                }
            }
        }

        $data_response['csrf'] = $form->get('csrf')->getValue();

        $response->setContent(
            Json::encode(
                $data_response
            )
        );

        return $response;
    }

    public function verifyExistDniAction()
    {

        $request = $this->getRequest();
        $response = $this->getResponse();
        $data_response = array('response' => false);
        $form = new BaseForm();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $form->setInputFilter($form->getInputFilter());
            $form->setData($post_data);

            if ($form->isValid()) {
                if ($this->getClienteTable()->getDocumento($post_data['dni']) > 0) {
                    $empresa = $this->getEmpresaTable()->getEmpresaSubDominio($post_data['subdominio']);
                    $valid = $this->getEmpresaClienteTable()
                        ->verifybyidClienteandidEmpresa($post_data['dni'], $empresa->id);

                    if ($valid) {
                        $data_response['response'] = true;
                        $data_response['empresa'] = $empresa->id;
                    }
                }
            }
        }

        $data_response['csrf'] = $form->get('csrf')->getValue();

        $response->setContent(
            Json::encode(
                $data_response
            )
        );

        return $response;
    }
}
