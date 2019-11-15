<?php
namespace Categoria;

use Categoria\Model\Categoria;
use Categoria\Model\CategoriaUbigeo;

use Categoria\Model\Table\CategoriaTable;
use Categoria\Model\Table\CategoriaUbigeoTable;

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
                //Categoria
                'Categoria\Model\Table\CategoriaTable' => function ($sm) {
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
                //CategoriaUbigeo
                'Categoria\Model\Table\CategoriaUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('CategoriaUbigeoTableGateway');
                    $table = new CategoriaUbigeoTable($tableGateway);
                    return $table;
                },
                'CategoriaUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CategoriaUbigeo());
                    return new TableGateway('BNF_CategoriaUbigeo', $dbAdapter, null, $resultSetPrototype);
                }
            ),
        );
    }
}
