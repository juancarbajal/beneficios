<?php
namespace Premios;

use Application\Service\MobileDetect;
use Premios\Model\AsignacionPremios;
use Premios\Model\AsignacionPremiosEstadoLog;
use Premios\Model\CuponPremios;
use Premios\Model\CuponPremiosLog;
use Premios\Model\LayoutPremios;
use Premios\Model\LayoutPremiosPosicion;
use Premios\Model\OfertaPremios;
use Premios\Model\OfertaPremiosAtributos;
use Premios\Model\OfertaPremiosRubro;
use Premios\Model\Table\AsignacionPremiosEstadoLogTable;
use Premios\Model\Table\AsignacionPremiosTable;
use Premios\Model\Table\CuponPremiosLogTable;
use Premios\Model\Table\CuponPremiosTable;
use Premios\Model\Table\LayoutPremiosPosicionTable;
use Premios\Model\Table\LayoutPremiosTable;
use Premios\Model\Table\OfertaPremiosAtributosTable;
use Premios\Model\Table\OfertaPremiosRubroTable;
use Premios\Model\Table\OfertaPremiosTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;

class Module
{
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
            $mobile = new MobileDetect();
            if ($mobile->isMobile() == 1) {
                $controller->layout('mobile/layout');
            } else {
                $controller->layout('layout/layout');
            }
        }, 100);
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                //AsignacionPremios
                'Premios\Model\Table\AsignacionPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionPremiosTableGateway');
                    $table = new AsignacionPremiosTable($tableGateway);
                    return $table;
                },
                'AsignacionPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AsignacionPremios());
                    return new TableGateway('BNF3_Asignacion_Premios', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios
                'Premios\Model\Table\OfertaPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosTableGateway');
                    $table = new OfertaPremiosTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremios());
                    return new TableGateway('BNF3_Oferta_Premios', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutPremios
                'Premios\Model\Table\LayoutPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPremiosTableGateway');
                    $table = new LayoutPremiosTable($tableGateway);
                    return $table;
                },
                'LayoutPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPremios());
                    return new TableGateway('BNF_LayoutPremios', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Atributos
                'Premios\Model\Table\OfertaPremiosAtributosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosAtributosTableGateway');
                    $table = new OfertaPremiosAtributosTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosAtributosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosAtributos());
                    return new TableGateway('BNF3_Oferta_Premios_Atributos', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutPremiosPosicion
                'Premios\Model\Table\LayoutPremiosPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPremiosPosicionTableGateway');
                    $table = new LayoutPremiosPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutPremiosPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPremiosPosicion());
                    return new TableGateway('BNF_LayoutPremiosPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaPremiosRubro
                'Premios\Model\Table\OfertaPremiosRubroTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosRubroTableGateway');
                    $table = new OfertaPremiosRubroTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosRubroTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosRubro());
                    return new TableGateway('BNF3_Oferta_Premios_Rubro', $dbAdapter, null, $resultSetPrototype);
                },
                //AsignacionEstadoLog
                'Premios\Model\Table\AsignacionPremiosEstadoLogTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionPremiosEstadoLogTableGateway');
                    $table = new AsignacionPremiosEstadoLogTable($tableGateway);
                    return $table;
                },
                'AsignacionPremiosEstadoLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AsignacionPremiosEstadoLog());
                    return new TableGateway('BNF3_Asignacion_Premios_Estado_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //CuponPremios
                'Premios\Model\Table\CuponPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('CuponPremiosTableGateway');
                    $table = new CuponPremiosTable($tableGateway);
                    return $table;
                },
                'CuponPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CuponPremios());
                    return new TableGateway('BNF3_Cupon_Premios', $dbAdapter, null, $resultSetPrototype);
                },
                //Cupon Premios Log
                'Premios\Model\Table\CuponPremiosLogTable' => function ($sm) {
                    $tableGateway = $sm->get('CuponPremiosLogTableGateway');
                    $table = new CuponPremiosLogTable($tableGateway);
                    return $table;
                },
                'CuponPremiosLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CuponPremiosLog());
                    return new TableGateway('BNF3_Cupon_Premios_Log', $dbAdapter, null, $resultSetPrototype);
                },
            )
        );
    }
}
