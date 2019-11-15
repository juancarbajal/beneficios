<?php
namespace Referido;

use Referido\Model\ClienteLanding;
use Referido\Model\ConfiguracionReferidos;
use Referido\Model\Referido;
use Referido\Model\Table\ClienteLandingTable;
use Referido\Model\Table\ConfiguracionReferidosTable;
use Referido\Model\Table\ReferidoTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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
                //Referido
                'Referido\Model\Table\ReferidoTable' => function ($sm) {
                    $tableGateway = $sm->get('ReferidoTableGateway');
                    $table = new ReferidoTable($tableGateway);
                    return $table;
                },
                'ReferidoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Referido());
                    return new TableGateway('BNF4_LandingReferidos', $dbAdapter, null, $resultSetPrototype);
                },
                //Cliente Landing
                'Referido\Model\Table\ClienteLandingTable' => function ($sm) {
                    $tableGateway = $sm->get('ClienteLandingTableGateway');
                    $table = new ClienteLandingTable($tableGateway);
                    return $table;
                },
                'ClienteLandingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ClienteLanding());
                    return new TableGateway('BNF4_LandingClientesColaboradores', $dbAdapter, null, $resultSetPrototype);
                },
                // Configuraciones Referido
                'Referido\Model\Table\ConfiguracionReferidosTable' => function ($sm) {
                    $tableGateway = $sm->get('ConfiguracionReferidosTableGateway');
                    $table = new ConfiguracionReferidosTable($tableGateway);
                    return $table;
                },
                'ConfiguracionReferidosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ConfiguracionReferidos());
                    return new TableGateway('BNF_Configuraciones_Referidos', $dbAdapter, null, $resultSetPrototype);
                },
            )
        );
    }
}
