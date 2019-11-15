<?php
namespace Auth;

use Auth\Model\ClienteCorreo;
use Auth\Model\EmpresaSegmento;
use Auth\Model\EmpresaSubgrupo;
use Auth\Model\Table\ClienteCorreoTable;
use Auth\Model\Table\EmpresaSegmentoTable;
use Auth\Model\Table\EmpresaSubgrupoTable;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

use Zend\Loader;
use Zend\ModuleManager\Feature;
use Zend\EventManager\EventInterface;

use Auth\Model\Cliente;
use Auth\Model\Empresa;
use Auth\Model\EmpresaClienteCliente;
use Auth\Model\Table\ClienteTable;
use Auth\Model\Table\EmpresaClienteClienteTable;
use Auth\Model\Table\EmpresaTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SessionManager;
use Zend\Validator\Csrf;

class Module
{
    private $layout;
    private $url_redirect;

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function init(ModuleManager $manager)
    {
        $events = $manager->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function ($e) {
            $controller = $e->getTarget();
            if ($this->isMobile() == 1) {
                $controller->layout('auth/layout/mobile');
            } else {
                $controller->layout('auth/layout');
            }
        }, 100);

        if($_SERVER['REQUEST_URI'] != "/"){
            $this->url_redirect = $_SERVER['REDIRECT_URL'];
        }
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'auth_service' => function ($sm) {
                    $config = $sm->get('Config');
                    $authService = new AuthenticationService(new SessionStorage('auth'));
                    $sessionContainer = new SessionContainer('auth');
                    $sessionContainer->setExpirationSeconds($config['time_session']);
                    $authService->setStorage(new SessionStorage('auth'));
                    return $authService;
                },
                'url_service' => function ($sm) {
                    $config = $sm->get('Config');
                    $urlService = new AuthenticationService(new SessionStorage('url'));
                    $sessionContainer = new SessionContainer('url');
                    $sessionContainer->setExpirationSeconds($config['time_session']);
                    $urlService->setStorage(new SessionStorage('url'));
                    return $urlService;
                },
                //Empresa
                'Auth\Model\Table\EmpresaTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaTableGateway');
                    $table = new EmpresaTable($tableGateway);
                    return $table;
                },
                'EmpresaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Empresa());
                    return new TableGateway('BNF_Empresa', $dbAdapter, null, $resultSetPrototype);
                },
                //Cliente
                'Auth\Model\Table\ClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('ClienteTableGateway');
                    $table = new ClienteTable($tableGateway);
                    return $table;
                },
                'ClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Cliente());
                    return new TableGateway('BNF_Cliente', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaClienteClienteTable
                'Auth\Model\Table\EmpresaClienteClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaClienteClienteTableGateway');
                    $table = new EmpresaClienteClienteTable($tableGateway);
                    return $table;
                },
                'EmpresaClienteClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaClienteCliente());
                    return new TableGateway('BNF_EmpresaClienteCliente', $dbAdapter, null, $resultSetPrototype);
                },
                //ClienteCorreo
                'Auth\Model\Table\ClienteCorreoTable' => function ($sm) {
                    $tableGateway = $sm->get('ClienteCorreoTableGateway');
                    $table = new ClienteCorreoTable($tableGateway);
                    return $table;
                },
                'ClienteCorreoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ClienteCorreo());
                    return new TableGateway('BNF_ClienteCorreo', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaSegmento
                'Auth\Model\Table\EmpresaSegmentoTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaSegmentoTableGateway');
                    $table = new EmpresaSegmentoTable($tableGateway);
                    return $table;
                },
                'EmpresaSegmentoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaSegmento());
                    return new TableGateway('BNF_EmpresaSegmento', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaSubgrupo
                'Auth\Model\Table\EmpresaSubgrupoTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaSubgrupoTableGateway');
                    $table = new EmpresaSubgrupoTable($tableGateway);
                    return $table;
                },
                'EmpresaSubgrupoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaSubgrupo());
                    return new TableGateway('BNF_Subgrupo', $dbAdapter, null, $resultSetPrototype);
                },
            )
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        //Iniciamos la lista de control de acceso
        $this->initAcl($e);
        //Comprobamos la lista de control de acceso
        $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));
        $e->getApplication()->getEventManager()->attach('dispatch', array($this, 'checkPost'));

        if(strpos($this->url_redirect, 'login') == false and strpos($this->url_redirect, '404') == false
            and strpos($this->url_redirect, 'verifyExist') == false
            and strpos($this->url_redirect, 'validate') == false
            and strpos($this->url_redirect, 'home') == false and $this->url_redirect != '') {
            $session = new SessionContainer('url');
            $data_url = $session->offsetGet('storage');
            $data_url['url'] = $this->url_redirect;
            $session->offsetSet('storage', $data_url);
        }
    }

    public function initAcl(MvcEvent $e)
    {
        //Creamos el objeto ACL
        $acl = new \Zend\Permissions\Acl\Acl();
        //Incluimos la lista de roles y permisos, nos devuelve un array
        $roles = require_once 'config/autoload/acl.roles.php';
        foreach ($roles as $role => $resources) {
            //Indicamos que el rol será genérico
            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            //Añadimos el rol al ACL
            $acl->addRole($role);
            //Recorremos los recursos o rutas permitidas
            foreach ($resources["allow"] as $resource) {
                //Si el recurso no existe lo añadimos
                if (!$acl->hasResource($resource)) {
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                }
                //Permitimos a ese rol ese recurso
                $acl->allow($role, $resource);
            }
            foreach ($resources["deny"] as $resource) {
                //Si el recurso no existe lo añadimos
                if (!$acl->hasResource($resource)) {
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                }
                //Denegamos a ese rol ese recurso
                $acl->deny($role, $resource);
            }
        }
        //Establecemos la lista de control de acceso
        $e->getViewModel()->acl = $acl;
    }

    public function checkPost(MvcEvent $e)
    {
        $request = $e->getRequest();
        $manager = new SessionManager();
        $storage = $manager->getStorage();
        if (isset($_SERVER['REQUEST_METHOD'])) {
            if ($request->isPost()) {

                $data_post = $request->getPost();
                $token = new Csrf();

                if (isset($data_post['csrf'])) {
                    if (!$token->isValid($data_post['csrf'])) {
                        $url = $e->getRequest()->getUri()->getPath();
                        $response = $e->getResponse();
                        $response->getHeaders()->addHeaderLine('Location', $url);
                        $response->setStatusCode(200);
                    }
                } else {
                    $url = $e->getRequest()->getUri()->getPath();
                    $response = $e->getResponse();
                    $response->getHeaders()->addHeaderLine('Location', $url);
                    $response->setStatusCode(200);
                }
                $token->setTimeout(0);
            }
            $storage->clear('Zend_Validator_Csrf_salt_csrf');
        }
    }

    public function checkAcl(MvcEvent $e)
    {
        //guardamos el nombre de la ruta o recurso a permitir o denegar
        $route = $e->getRouteMatch()->getMatchedRouteName();
        //Instanciamos el servicio de autenticacion
        $auth = $e->getApplication()->getServiceManager()->get('auth_service');
        $identi = $auth->getStorage()->read();

        // Establecemos nuestro rol
        // $userRole = 'admin';
        // Si el usuario esta identificado le asignaremos el rol admin y si no el rol visitante.

        if (!isset($identi)) {
            $userRole = "guest";
        } else {
            if ($identi['Tipo'] == 1) {
                $userRole = "cliente";
            } else {
                $userRole = "guest";
            }
        }


        // Esto se puede mejorar fácilmente, si tenemos un campo rol en la BD cuando el usuario
        // se identifique en la sesión se guardarán todos los datos del mismo, de modo que
        //  $userRole=$identi->role;

        //Comprobamos si no está permitido para ese rol esa ruta
        if (!$e->getViewModel()->acl->isAllowed($userRole, $route)) {
            //Devolvemos un error 404
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/');
            $response->setStatusCode(302);
        }
    }

    public function isMobile()
    {
        return 0;/*
        $tablet_browser = 0;
        $mobile_browser = 0;

        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $tablet_browser++;
        }

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda ', 'xda-');

        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'opera mini') > 0) {
            $mobile_browser++;
            //Check for tablets on opera mini alternative headers
            $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])
                ? $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'] : (isset($_SERVER['HTTP_DEVICE_STOCK_UA'])
                    ? $_SERVER['HTTP_DEVICE_STOCK_UA'] : ''));
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
                $tablet_browser++;
            }
        }
        if ($tablet_browser > 0) {
            return 2;
        } else if ($mobile_browser > 0) {
            return 1;
        } else {
            return 0;
            }*/
    }
}
