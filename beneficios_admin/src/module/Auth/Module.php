<?php
namespace Auth;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

use Zend\Loader;
use Zend\ModuleManager\Feature;
use Zend\EventManager\EventInterface;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SessionManager;
use Zend\Validator\Csrf;

class Module
{
    public function init(ModuleManager $mm)
    {
        $mm->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__,
            'dispatch',
            function ($e) {
                $e->getTarget()->layout('auth/layout');
            }
        );
    }

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
                }
            )
        );
    }

    //Este método se ejecuta cada vez que carga una página
    public function onBootstrap(MvcEvent $e)
    {
        //Iniciamos la lista de control de acceso
        $this->initAcl($e);
        //Comprobamos la lista de control de acceso
        $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));
        $e->getApplication()->getEventManager()->attach('dispatch', array($this, 'checkPost'));

        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        if (empty($data_user) || is_null($data_user)) {
            header('Location: /', true);
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
            $userRole = $identi->TipoUsuario;
        }


        // Esto se puede mejorar fácilmente, si tenemos un campo rol en la BD cuando el usuario
        // se identifique en la sesión se guardarán todos los datos del mismo, de modo que
        //  $userRole=$identi->role;

        //Comprobamos si no está permitido para ese rol esa ruta
        if (!$e->getViewModel()->acl->isAllowed($userRole, $route)) {
            //Devolvemos un error 404
            $response = $e->getResponse();
            $session = new SessionContainer('auth');
            $data_user = $session->offsetGet('storage');
            if (!isset($data_user->Correo)) {
                $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/');
            } else {
                $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/no-access');

            }
            $response->setStatusCode(302);
        }
    }

    public function checkPost(MvcEvent $e)
    {
        $request = $e->getRequest();
        $manager = new SessionManager();
        $storage = $manager->getStorage();
        $response = $e->getResponse();
        if(isset($_SERVER['REQUEST_METHOD'])){
            if ($request->isPost()){

                $auth = $e->getApplication()->getServiceManager()->get('auth_service');
                $identi = $auth->getStorage()->read();

                if (!isset($identi)) {
                    $auth->clearIdentity();
                    $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/');
                    $response->setStatusCode(302);
                }

                $data_post = $request->getPost();
                $token = new Csrf();

                if (isset($data_post['csrf'])){
                    if ( !$token->isValid( $data_post['csrf'])){
                        $url = $e->getRequest()->getUri()->getPath();
                        $response = $e->getResponse();
                        $response->getHeaders()->addHeaderLine('Location', $url);
                        $response->setStatusCode(200);
                    }
                }else {
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
}
