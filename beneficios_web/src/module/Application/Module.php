<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\Asignacion;
use Application\Model\AsignacionEstadoLog;
use Application\Model\Banner;
use Application\Model\BannersCampanias;
use Application\Model\BannersCategoria;
use Application\Model\BannersTienda;
use Application\Model\Campania;
use Application\Model\Configuraciones;
use Application\Model\Cupon;
use Application\Model\CuponPuntosAsignacion;
use Application\Model\CuponPuntosLog;
use Application\Model\DeliveryPuntos;
use Application\Model\DetalleOfertaFormulario;
use Application\Model\FormularioLead;
use Application\Model\Galeria;
use Application\Model\Categoria;
use Application\Model\LayoutCampania;
use Application\Model\LayoutCampaniaPosicion;
use Application\Model\LayoutCategoria;
use Application\Model\LayoutCategoriaPosicion;
use Application\Model\LayoutPuntos;
use Application\Model\LayoutPuntosPosicion;
use Application\Model\LayoutTienda;
use Application\Model\LayoutTiendaPosicion;
use Application\Model\Oferta;
use Application\Model\OfertaAtributos;
use Application\Model\OfertaCuponCodigo;
use Application\Model\OfertaEmpresaCliente;
use Application\Model\OfertaFormCliente;
use Application\Model\OfertaFormClienteLead;
use Application\Model\OfertaPuntos;
use Application\Model\OfertaPuntosAtributos;
use Application\Model\OfertaPuntosDelivery;
use Application\Model\OfertaPuntosRubro;
use Application\Model\OfertaUbigeo;
use Application\Model\Preguntas;
use Application\Model\Table\AsignacionEstadoLogTable;
use Application\Model\Table\AsignacionTable;
use Application\Model\Table\BannersCampaniasTable;
use Application\Model\Table\BannersCategoriaTable;
use Application\Model\Table\BannersTiendaTable;
use Application\Model\Table\BannerTable;
use Application\Model\Table\CampaniaTable;
use Application\Model\Table\ConfiguracionesTable;
use Application\Model\Table\CuponPuntosAsignacionTable;
use Application\Model\Table\CuponPuntosLogTable;
use Application\Model\Table\CuponTable;
use Application\Model\Table\DeliveryPuntosTable;
use Application\Model\Table\DetalleOfertaFormularioTable;
use Application\Model\Table\FormularioLeadTable;
use Application\Model\Table\GaleriaTable;
use Application\Model\Table\CategoriaTable;
use Application\Model\Table\LayoutCampaniaPosicionTable;
use Application\Model\Table\LayoutCampaniaTable;
use Application\Model\Table\LayoutCategoriaPosicionTable;
use Application\Model\Table\LayoutCategoriaTable;
use Application\Model\Table\LayoutPuntosPosicionTable;
use Application\Model\Table\LayoutPuntosTable;
use Application\Model\Table\LayoutTiendaPosicionTable;
use Application\Model\Table\LayoutTiendaTable;
use Application\Model\Table\OfertaAtributosTable;
use Application\Model\Table\OfertaCuponCodigoTable;
use Application\Model\Table\OfertaEmpresaClienteTable;
use Application\Model\Table\OfertaFormClienteLeadTable;
use Application\Model\Table\OfertaFormClienteTable;
use Application\Model\Table\OfertaPuntosAtributosTable;
use Application\Model\Table\OfertaPuntosDeliveryTable;
use Application\Model\Table\OfertaPuntosRubroTable;
use Application\Model\Table\OfertaPuntosTable;
use Application\Model\Table\OfertaTable;
use Application\Model\Table\OfertaUbigeoTable;
use Application\Model\Table\PreguntasTable;
use Application\Model\Table\TarjetasOfertaTable;
use Application\Model\Table\UbigeoTable;
use Application\Model\TarjetasOferta;
use Application\Model\Ubigeo;
use Application\Model\OfertaFormulario;
use Application\Model\Table\OfertaFormularioTable;
use Application\Service\MobileDetect;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManager;
use Zend\Mail\Transport\Smtp;
use Zend\Session\Container as SessionContainer;

class Module
{
    private $layout;

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        if (!isset($data_user['Empresa'])) {
            header('Location: /');
        }

        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'handleError'), 500);

        $sharedManager = $eventManager->getSharedManager();
        $sharedManager->attach(
            'Zend\Mvc\Controller\AbstractActionController',
            'dispatch',
            array($this, 'handleControllerCannotDispatchRequest'),
            101
        );

        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(
            'dispatch.error',
            array($this,
                'handleControllerNotFoundAndControllerInvalidAndRouteNotFound'),
            100
        );
    }

    public function handleError(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('Config');
        if ($config["debug_mode"] == true) {
            $viewModel = $e->getViewModel();
            $viewModel->setTemplate('layout/layout');
        } else {
            $viewModel = $e->getViewModel();
            $viewModel->setTemplate('error/errorcustom');
        }
    }

    public function handleControllerCannotDispatchRequest(MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('action');
        $controller = get_class($e->getTarget());

        // error-controller-cannot-dispatch
        if (!method_exists($e->getTarget(), $action . 'Action')) {
            return $this->redirect404($e);
        }
    }

    public function handleControllerNotFoundAndControllerInvalidAndRouteNotFound(MvcEvent $e)
    {
        $return = null;
        $error = $e->getError();
        if ($error == Application::ERROR_CONTROLLER_NOT_FOUND) {
            $return = $this->redirect404($e);
        }

        if ($error == Application::ERROR_CONTROLLER_INVALID) {
            $return = $this->redirect404($e);
        }

        if ($error == Application::ERROR_ROUTER_NO_MATCH) {
            $return = $this->redirect404($e);
        }
        //return $return;
        exit;
    }

    public function redirect404(MvcEvent $e)
    {
        $url = $e->getRouter()->assemble(array('action' => 'index'), array('name' => '404'));
        $response = $e->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        $response->sendHeaders();
        return $response;
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
                'mail.transport' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    $transport = new Smtp();
                    $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
                    return $transport;
                },
                //Oferta
                'Application\Model\Table\OfertaTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaTableGateway');
                    $table = new OfertaTable($tableGateway);
                    return $table;
                },
                'OfertaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Oferta());
                    return new TableGateway('BNF_Oferta', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaEmpresaCliente
                'Application\Model\Table\OfertaEmpresaClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaEmpresaClienteTableGateway');
                    $table = new OfertaEmpresaClienteTable($tableGateway);
                    return $table;
                },
                'OfertaEmpresaClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaEmpresaCliente());
                    return new TableGateway('BNF_OfertaEmpresaCliente', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Ubigeo
                'Application\Model\Table\OfertaUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaUbigeoTableGateway');
                    $table = new OfertaUbigeoTable($tableGateway);
                    return $table;
                },
                'OfertaUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaUbigeo());
                    return new TableGateway('BNF_OfertaUbigeo', $dbAdapter, null, $resultSetPrototype);
                },
                //Layout Categoria
                'Application\Model\Table\LayoutCategoriaTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCategoriaTableGateway');
                    $table = new LayoutCategoriaTable($tableGateway);
                    return $table;
                },
                'LayoutCategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCategoria());
                    return new TableGateway('BNF_LayoutCategoria', $dbAdapter, null, $resultSetPrototype);
                },
                //Layout Campaña
                'Application\Model\Table\LayoutCampaniaTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCampaniaTableGateway');
                    $table = new LayoutCampaniaTable($tableGateway);
                    return $table;
                },
                'LayoutCampaniaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCampania());
                    return new TableGateway('BNF_LayoutCampania', $dbAdapter, null, $resultSetPrototype);
                },
                //Ubigeo
                'Application\Model\UbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('UbigeoTableGateway');
                    $table = new UbigeoTable($tableGateway);
                    return $table;
                },
                'UbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Ubigeo());
                    return new TableGateway('BNF_Ubigeo', $dbAdapter, null, $resultSetPrototype);
                },
                //Galeria
                'Application\Model\Table\GaleriaTable' => function ($sm) {
                    $tableGateway = $sm->get('GaleriaTableGateway');
                    $table = new GaleriaTable($tableGateway);
                    return $table;
                },
                'GaleriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Galeria());
                    return new TableGateway('BNF_Galeria', $dbAdapter, null, $resultSetPrototype);
                },
                //Banners
                'Application\Model\Table\BannerTable' => function ($sm) {
                    $tableGateway = $sm->get('BannerTableGateway');
                    $table = new BannerTable($tableGateway);
                    return $table;
                },
                'BannerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Banner());
                    return new TableGateway('BNF_Banners', $dbAdapter, null, $resultSetPrototype);
                },
                //Categoria
                'Application\Model\Table\CategoriaTable' => function ($sm) {
                    $tableGateway = $sm->get('CategoriaTableGateway');
                    $table = new CategoriaTable($tableGateway);
                    return $table;
                },
                'CategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Categoria());
                    return new TableGateway('BNF_Categoria', $dbAdapter, null, $resultSetPrototype);
                },
                //Campania
                'Application\Model\Table\CampaniaTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniaTableGateway');
                    $table = new CampaniaTable($tableGateway);
                    return $table;
                },
                'CampaniaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Campania());
                    return new TableGateway('BNF_Campanias', $dbAdapter, null, $resultSetPrototype);
                },
                //BannerCampanias
                'Application\Model\Table\BannersCampaniasTable' => function ($sm) {
                    $tableGateway = $sm->get('BannersCampaniasTableGateway');
                    $table = new BannersCampaniasTable($tableGateway);
                    return $table;
                },
                'BannersCampaniasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BannersCampanias());
                    return new TableGateway('BNF_BannersCampanias', $dbAdapter, null, $resultSetPrototype);
                },
                //BannerCategoria
                'Application\Model\Table\BannersCategoriaTable' => function ($sm) {
                    $tableGateway = $sm->get('BannersCategoriaTableGateway');
                    $table = new BannersCategoriaTable($tableGateway);
                    return $table;
                },
                'BannersCategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BannersCategoria());
                    return new TableGateway('BNF_BannersCategoria', $dbAdapter, null, $resultSetPrototype);
                },
                //BannerTienda
                'Application\Model\Table\BannersTiendaTable' => function ($sm) {
                    $tableGateway = $sm->get('BannersTiendaTableGateway');
                    $table = new BannersTiendaTable($tableGateway);
                    return $table;
                },
                'BannersTiendaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BannersTienda());
                    return new TableGateway('BNF_BannersTienda', $dbAdapter, null, $resultSetPrototype);
                },
                //Cupon
                'Application\Model\Table\CuponTable' => function ($sm) {
                    $tableGateway = $sm->get('CuponTableGateway');
                    $table = new CuponTable($tableGateway);
                    return $table;
                },
                'CuponTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Cupon());
                    return new TableGateway('BNF_Cupon', $dbAdapter, null, $resultSetPrototype);
                },
                //Layout Tienda
                'Application\Model\Table\LayoutTiendaTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutTiendaTableGateway');
                    $table = new LayoutTiendaTable($tableGateway);
                    return $table;
                },
                'LayoutTiendaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutTienda());
                    return new TableGateway('BNF_LayoutTienda', $dbAdapter, null, $resultSetPrototype);
                },
                //Configuraciones
                'Application\Model\Table\ConfiguracionesTable' => function ($sm) {
                    $tableGateway = $sm->get('ConfiguracionesTableGateway');
                    $table = new ConfiguracionesTable($tableGateway);
                    return $table;
                },
                'ConfiguracionesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Configuraciones());
                    return new TableGateway('BNF_Configuraciones', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaFormulario
                'Application\Model\Table\OfertaFormularioTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaFormularioTableGateway');
                    $table = new OfertaFormularioTable($tableGateway);
                    return $table;
                },
                'OfertaFormularioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaFormulario());
                    return new TableGateway('BNF_OfertaFormulario', $dbAdapter, null, $resultSetPrototype);
                },
                //DetalleOfertaFormulario
                'Application\Model\Table\DetalleOfertaFormularioTable' => function ($sm) {
                    $tableGateway = $sm->get('DetalleOfertaFormularioTableGateway');
                    $table = new DetalleOfertaFormularioTable($tableGateway);
                    return $table;
                },
                'DetalleOfertaFormularioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DetalleOfertaFormulario());
                    return new TableGateway('BNF_DetalleOfertaFormulario', $dbAdapter, null, $resultSetPrototype);
                },
                //DetalleOfertaFormulario
                'Application\Model\Table\OfertaFormClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaFormClienteTableGateway');
                    $table = new OfertaFormClienteTable($tableGateway);
                    return $table;
                },
                'OfertaFormClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaFormCliente());
                    return new TableGateway('BNF_OfertaFormCliente', $dbAdapter, null, $resultSetPrototype);
                },
                //PreguntasCliente
                'Application\Model\Table\PreguntasTable' => function ($sm) {
                    $tableGateway = $sm->get('PreguntasTableGateway');
                    $table = new PreguntasTable($tableGateway);
                    return $table;
                },
                'PreguntasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Preguntas());
                    return new TableGateway('BNF_Preguntas', $dbAdapter, null, $resultSetPrototype);
                },
                //FormularioLead
                'Application\Model\Table\FormularioLeadTable' => function ($sm) {
                    $tableGateway = $sm->get('FormularioLeadTableGateway');
                    $table = new FormularioLeadTable($tableGateway);
                    return $table;
                },
                'FormularioLeadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FormularioLead());
                    return new TableGateway('BNF_FormularioLead', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaFormClienteLead
                'Application\Model\Table\OfertaFormClienteLeadTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaFormClienteLeadTableGateway');
                    $table = new OfertaFormClienteLeadTable($tableGateway);
                    return $table;
                },
                'OfertaFormClienteLeadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaFormClienteLead());
                    return new TableGateway('BNF_OfertaFormClienteLead', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaFormClienteLead
                'Application\Model\Table\TarjetasOfertaTable' => function ($sm) {
                    $tableGateway = $sm->get('TarjetasOfertaTableGateway');
                    $table = new TarjetasOfertaTable($tableGateway);
                    return $table;
                },
                'TarjetasOfertaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TarjetasOferta());
                    return new TableGateway('BNF_Tarjetas_Oferta', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutCategoriaPosicion
                'Application\Model\Table\LayoutCategoriaPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCategoriaPosicionTableGateway');
                    $table = new LayoutCategoriaPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutCategoriaPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCategoriaPosicion());
                    return new TableGateway('BNF_LayoutCategoriaPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutCampaniaPosicion
                'Application\Model\Table\LayoutCampaniaPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCampaniaPosicionTableGateway');
                    $table = new LayoutCampaniaPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutCampaniaPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCampaniaPosicion());
                    return new TableGateway('BNF_LayoutCampaniaPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutTiendaPosicionTable
                'Application\Model\Table\LayoutTiendaPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutTiendaPosicionTableGateway');
                    $table = new LayoutTiendaPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutTiendaPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutTiendaPosicion());
                    return new TableGateway('BNF_LayoutTiendaPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //Asignacion
                'Application\Model\Table\AsignacionTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionTableGateway');
                    $table = new AsignacionTable($tableGateway);
                    return $table;
                },
                'AsignacionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Asignacion());
                    return new TableGateway('BNF2_Asignacion_Puntos', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos
                'Application\Model\Table\OfertaPuntosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosTableGateway');
                    $table = new OfertaPuntosTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntos());
                    return new TableGateway('BNF2_Oferta_Puntos', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos
                'Application\Model\Table\OfertaPuntosAtributosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosAtributosTableGateway');
                    $table = new OfertaPuntosAtributosTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosAtributosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosAtributos());
                    return new TableGateway('BNF2_Oferta_Puntos_Atributos', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutPuntos
                'Application\Model\Table\LayoutPuntosTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPuntosTableGateway');
                    $table = new LayoutPuntosTable($tableGateway);
                    return $table;
                },
                'LayoutPuntosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPuntos());
                    return new TableGateway('BNF_LayoutPuntos', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutPuntosPosicion
                'Application\Model\Table\LayoutPuntosPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPuntosPosicionTableGateway');
                    $table = new LayoutPuntosPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutPuntosPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPuntosPosicion());
                    return new TableGateway('BNF_LayoutPuntosPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaPuntosRubro
                'Application\Model\Table\OfertaPuntosRubroTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosRubroTableGateway');
                    $table = new OfertaPuntosRubroTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosRubroTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosRubro());
                    return new TableGateway('BNF2_Oferta_Puntos_Rubro', $dbAdapter, null, $resultSetPrototype);
                },
                //AsignacionEstadoLog
                'Application\Model\Table\AsignacionEstadoLogTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionEstadoLogTableGateway');
                    $table = new AsignacionEstadoLogTable($tableGateway);
                    return $table;
                },
                'AsignacionEstadoLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AsignacionEstadoLog());
                    return new TableGateway('BNF2_Asignacion_Puntos_Estado_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //Cupon Puntos Log
                'Application\Model\Table\CuponPuntosLogTable' => function ($sm) {
                    $tableGateway = $sm->get('CuponPuntosLogTableGateway');
                    $table = new CuponPuntosLogTable($tableGateway);
                    return $table;
                },
                'CuponPuntosLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CuponPuntosLog());
                    return new TableGateway('BNF2_Cupon_Puntos_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Atributos
                'Application\Model\Table\OfertaAtributosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaAtributosTableGateway');
                    $table = new OfertaAtributosTable($tableGateway);
                    return $table;
                },
                'OfertaAtributosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaAtributos());
                    return new TableGateway('BNF_Oferta_Atributos', $dbAdapter, null, $resultSetPrototype);
                },
                //CuponPuntosAsignacion
                'Application\Model\Table\CuponPuntosAsignacionTable' => function ($sm) {
                    $tableGateway = $sm->get('CuponPuntosAsignacionTableGateway');
                    $table = new CuponPuntosAsignacionTable($tableGateway);
                    return $table;
                },
                'CuponPuntosAsignacionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CuponPuntosAsignacion());
                    return new TableGateway('BNF2_Cupon_Puntos_Asignacion', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaCuponCodigo
                'Oferta\Model\Table\OfertaCuponCodigoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaCuponCodigoTableGateway');
                    $table = new OfertaCuponCodigoTable($tableGateway);
                    return $table;
                },
                'OfertaCuponCodigoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaCuponCodigo());
                    return new TableGateway('BNF_Oferta_Cupon_Codigo', $dbAdapter, null, $resultSetPrototype);
                },
                //DeliveryPuntos
                'Application\Model\Table\DeliveryPuntosTable' => function ($sm) {
                    $tableGateway = $sm->get('DeliveryPuntosTableGateway');
                    $table = new DeliveryPuntosTable($tableGateway);
                    return $table;
                },
                'DeliveryPuntosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DeliveryPuntos());
                    return new TableGateway('BNF2_Delivery_Puntos', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaDeliveryPuntos
                'Application\Model\Table\OfertaPuntosDeliveryTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosDeliveryTableGateway');
                    $table = new OfertaPuntosDeliveryTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosDeliveryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosDelivery());
                    return new TableGateway('BNF2_Oferta_Puntos_Delivery', $dbAdapter, null, $resultSetPrototype);
                },
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'configItem' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ConfigItem();
                    $viewHelper->setServiceLocator($serviceLocator);

                    return $viewHelper;
                }
            ),
        );
    }

    public function init(ModuleManager $manager)
    {
        $events = $manager->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function ($e) {
            $controller = $e->getTarget();
            $mobile = new MobileDetect();
            if ($mobile->isMobile() == 1) {
                $controller->layout('mobile/layout');
            } else {
                $controller->layout('layout/layout');
            }
        }, 100);
    }
}
