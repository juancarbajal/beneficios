<?php
namespace Reportes;

use Reportes\Model\DmMetCliente;
use Reportes\Model\DmMetClientePreguntas;
use Reportes\Model\OfertaFormCliente;
use Reportes\Model\Table\DmMetClientePreguntasTable;
use Reportes\Model\Table\DmMetClienteTable;
use Reportes\Model\Table\OfertaFormClienteTable;
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
                //OfertaFormCliente
                'Reportes\Model\Table\OfertaFormClienteTable' => function ($sm) {
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
                //DM_Met_Cliente
                'Reportes\Model\Table\DmMetClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('DmMetClienteTableGateway');
                    $table = new DmMetClienteTable($tableGateway);
                    return $table;
                },
                'DmMetClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DmMetCliente());
                    return new TableGateway('BNF_DM_Met_Cliente', $dbAdapter, null, $resultSetPrototype);
                },
                //DM_Met_Cliente_Preguntas
                'Reportes\Model\Table\DmMetClientePreguntasTable' => function ($sm) {
                    $tableGateway = $sm->get('DmMetClientePreguntasTableGateway');
                    $table = new DmMetClientePreguntasTable($tableGateway);
                    return $table;
                },
                'DmMetClientePreguntasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DmMetClientePreguntas());
                    return new TableGateway('BNF_DM_Met_Cliente_Preguntas', $dbAdapter, null, $resultSetPrototype);
                },
            )
        );
    }
}
