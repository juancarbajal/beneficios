<?php
namespace Rubro;

use Rubro\Model\Rubro;
use Rubro\Model\Table\RubroTable;

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
                //Rubro
                'Rubro\Model\Table\RubroTable' => function ($sm) {
                    $tableGateway = $sm->get('RubroTableGateway');
                    $table = new RubroTable($tableGateway);
                    return $table;
                },
                'RubroTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Rubro());
                    return new TableGateway('BNF_Rubro', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
