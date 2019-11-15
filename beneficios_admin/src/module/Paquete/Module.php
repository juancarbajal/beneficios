<?php
namespace Paquete;

use Paquete\Model\Paquete;
use Paquete\Model\PaqueteTable;
use Paquete\Model\TipoPaquete;
use Paquete\Model\TipoPaqueteTable;
use Paquete\Model\Pais;
use Paquete\Model\PaisTable;
use Paquete\Model\PaquetePais;
use Paquete\Model\PaquetePaisTable;
use Paquete\Model\PaqueteEmpresaProveedor;
use Paquete\Model\PaqueteEmpresaProveedorTable;
use Paquete\Model\BolsaTotal;
use Paquete\Model\Table\BolsaTotalTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(

                //Paquete
                'Paquete\Model\PaqueteTable' => function ($sm) {
                    $tableGateway = $sm->get('PaqueteTableGateway');
                    $table = new PaqueteTable($tableGateway);
                    return $table;
                },
                'PaqueteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Paquete());
                    return new TableGateway('BNF_Paquete', $dbAdapter, null, $resultSetPrototype);
                },
                //TipoPaquete
                'Paquete\Model\TipoPaqueteTable' => function ($sm) {
                    $tableGateway = $sm->get('TipoPaqueteTableGateway');
                    $table = new TipoPaqueteTable($tableGateway);
                    return $table;
                },
                'TipoPaqueteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TipoPaquete());
                    return new TableGateway('BNF_TipoPaquete', $dbAdapter, null, $resultSetPrototype);
                },
                //Pais
                'Paquete\Model\PaisTable' => function ($sm) {
                    $tableGateway = $sm->get('PaisTableGateway');
                    $table = new PaisTable($tableGateway);
                    return $table;
                },
                'PaisTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Pais());
                    return new TableGateway('BNF_Pais', $dbAdapter, null, $resultSetPrototype);
                },
                //PaquetePais
                'Paquete\Model\PaquetePaisTable' => function ($sm) {
                    $tableGateway = $sm->get('PaquetePaisTableGateway');
                    $table = new PaquetePaisTable($tableGateway);
                    return $table;
                },
                'PaquetePaisTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PaquetePais());
                    return new TableGateway('BNF_PaquetePais', $dbAdapter, null, $resultSetPrototype);
                },
                //PaqueteEmpresaProveedor
                'Paquete\Model\PaqueteEmpresaProveedorTable' => function ($sm) {
                    $tableGateway = $sm->get('PaqueteEmpresaProveedorTableGateway');
                    $table = new PaqueteEmpresaProveedorTable($tableGateway);
                    return $table;
                },
                'PaqueteEmpresaProveedorTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PaqueteEmpresaProveedor());
                    return new TableGateway('BNF_PaqueteEmpresaProveedor', $dbAdapter, null, $resultSetPrototype);
                },
                //BolsaTotal
                'Paquete\Model\Table\BolsaTotalTable' => function ($sm) {
                    $tableGateway = $sm->get('BolsaTotalTableGateway');
                    $table = new BolsaTotalTable($tableGateway);
                    return $table;
                },
                'BolsaTotalTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BolsaTotal());
                    return new TableGateway('BNF_BolsaTotal', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
