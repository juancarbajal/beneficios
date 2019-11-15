<?php
namespace Cupon;

use Cupon\Model\Configuraciones;
use Cupon\Model\Cupon;
use Cupon\Model\CuponPremios;
use Cupon\Model\CuponPremiosLog;
use Cupon\Model\CuponPuntos;
use Cupon\Model\CuponPuntosLog;
use Cupon\Model\Table\ConfiguracionesTable;
use Cupon\Model\Table\CuponPremiosLogTable;
use Cupon\Model\Table\CuponPremiosTable;
use Cupon\Model\Table\CuponPuntosLogTable;
use Cupon\Model\Table\CuponPuntosTable;
use Cupon\Model\Table\CuponTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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
                //Cupon
                'Cupon\Model\Table\CuponTable' => function ($sm) {
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
                //Configuraciones
                'Cupon\Model\Table\ConfiguracionesTable' => function ($sm) {
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
                //Cupon Puntos
                'Cupon\Model\Table\CuponPuntosTable' => function ($sm) {
                    $tableGateway = $sm->get('CuponPuntosTableGateway');
                    $table = new CuponPuntosTable($tableGateway);
                    return $table;
                },
                'CuponPuntosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CuponPuntos());
                    return new TableGateway('BNF2_Cupon_Puntos', $dbAdapter, null, $resultSetPrototype);
                },
                //Cupon Puntos Log
                'Cupon\Model\Table\CuponPuntosLogTable' => function ($sm) {
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
                //Cupon Premios
                'Cupon\Model\Table\CuponPremiosTable' => function ($sm) {
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
                'Cupon\Model\Table\CuponPremiosLogTable' => function ($sm) {
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
