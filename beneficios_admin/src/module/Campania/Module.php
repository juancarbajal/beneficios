<?php
namespace Campania;

use Campania\Model\Campania;
use Campania\Model\Table\CampaniaTable;
use Campania\Model\CampaniaUbigeo;
use Campania\Model\Table\CampaniaUbigeoTable;

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
                //Campania
                'Campania\Model\Table\CampaniaTable' => function ($sm) {
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
                //CampaniaUbigeo
                'Campania\Model\Table\CampaniaUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniaUbigeoTableGateway');
                    $table = new CampaniaUbigeoTable($tableGateway);
                    return $table;
                },
                'CampaniaUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CampaniaUbigeo());
                    return new TableGateway('BNF_CampaniaUbigeo', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
