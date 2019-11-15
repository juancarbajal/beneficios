<?php
namespace Empresa;

use Empresa\Model\Empresa;
use Empresa\Model\EmpresaTable;
use Empresa\Model\EmpresaSegmento;
use Empresa\Model\EmpresaSegmentoTable;
use Empresa\Model\EmpresaSubgrupo;
use Empresa\Model\EmpresaSubgrupoTable;
use Empresa\Model\EmpresaTipoEmpresa;
use Empresa\Model\EmpresaTipoEmpresaTable;
use Empresa\Model\TipoEmpresa;
use Empresa\Model\TipoEmpresaTable;
use Empresa\Model\Ubigeo;
use Empresa\Model\UbigeoTable;
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
                //Empresa
                'Empresa\Model\EmpresaTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaTableGateway');
                    $table = new EmpresaTable($tableGateway);
                    return $table;
                },
                'EmpresaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Empresa());
                    return new TableGateway('BNF_Empresa', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaSegmento
                'Empresa\Model\EmpresaSegmentoTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaSegmentoTableGateway');
                    $table = new EmpresaSegmentoTable($tableGateway);
                    return $table;
                },
                'EmpresaSegmentoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaSegmento());
                    return new TableGateway('BNF_EmpresaSegmento', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaSubgrupo
                'Empresa\Model\EmpresaSubgrupoTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaSubgrupoTableGateway');
                    $table = new EmpresaSubgrupoTable($tableGateway);
                    return $table;
                },
                'EmpresaSubgrupoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaSubgrupo());
                    return new TableGateway('BNF_EmpresaSubgrupo', $dbAdapter, null, $resultSetPrototype);
                },
                //EmpresaTipoEmpresa
                'Empresa\Model\EmpresaTipoEmpresaTable' => function ($sm) {
                    $tableGateway = $sm->get('EmpresaTipoEmpresaTableGateway');
                    $table = new EmpresaTipoEmpresaTable($tableGateway);
                    return $table;
                },
                'EmpresaTipoEmpresaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmpresaTipoEmpresa());
                    return new TableGateway('BNF_EmpresaTipoEmpresa', $dbAdapter, null, $resultSetPrototype);
                },
                //TipoEmpresa
                'Empresa\Model\TipoEmpresaTable' => function ($sm) {
                    $tableGateway = $sm->get('TipoEmpresaTableGateway');
                    $table = new TipoEmpresaTable($tableGateway);
                    return $table;
                },
                'TipoEmpresaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TipoEmpresa());
                    return new TableGateway('BNF_TipoEmpresa', $dbAdapter, null, $resultSetPrototype);
                },
                //Ubigeo
                'Empresa\Model\UbigeoTable' => function ($sm) {
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
            ),
        );
    }
}
