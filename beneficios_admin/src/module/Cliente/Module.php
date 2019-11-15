<?php
namespace Cliente;

use Cliente\Model\Cliente;
use Cliente\Model\ClienteTable;
use Cliente\Model\EmpresaSegmentoCliente;
use Cliente\Model\EmpresaSegmentoClienteTable;
use Cliente\Model\EmpresaSubgrupoCliente;
use Cliente\Model\EmpresaSubgrupoClienteTable;
use Cliente\Model\EmpresaClienteCliente;
use Cliente\Model\EmpresaClienteClienteTable;

use Cliente\Model\Preguntas;
use Cliente\Model\Table\PreguntasTable;
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
                //Cliente
                'Cliente\Model\ClienteTable' => function ($sm) {
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
                //EmpresaSegmentoClienteTable
                'Cliente\Model\EmpresaSegmentoClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaSegmentoClienteTableGateway');
                    $table = new EmpresaSegmentoClienteTable($tableGateway);
                    return $table;
                },
                'EmpresaSegmentoClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaSegmentoCliente());
                    return new TableGateway('BNF_EmpresaSegmentoCliente', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaSubgrupoClienteTable
                'Cliente\Model\EmpresaSubgrupoClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaSubgrupoClienteTableGateway');
                    $table = new EmpresaSubgrupoClienteTable($tableGateway);
                    return $table;
                },
                'EmpresaSubgrupoClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaSubgrupoCliente());
                    return new TableGateway('BNF_EmpresaSubgrupoCliente', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaClienteClienteTable
                'Cliente\Model\EmpresaClienteClienteTable' => function ($sm) {
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
                //PreguntasCliente
                'Cliente\Model\Table\PreguntasTable' => function ($sm) {
                    $tableGateway = $sm->get('PreguntasTableGateway');
                    $table = new PreguntasTable($tableGateway);
                    return $table;
                },
                'PreguntasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Preguntas());
                    return new TableGateway('BNF_Preguntas', $dbAdapter, null, $resultSetPrototype);
                }
            ),
        );
    }
}
