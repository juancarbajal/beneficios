<?php
namespace Demanda;

use Demanda\Model\Demanda;
use Demanda\Model\DemandaEmpresas;
use Demanda\Model\DemandaDepartamentos;
use Demanda\Model\DemandaEmpresasAdicionales;
use Demanda\Model\DemandaLog;
use Demanda\Model\DemandaPremios;
use Demanda\Model\DemandaPremiosDepartamentos;
use Demanda\Model\DemandaPremiosEmpresas;
use Demanda\Model\DemandaPremiosEmpresasAdicionales;
use Demanda\Model\DemandaPremiosLog;
use Demanda\Model\DemandaPremiosRubros;
use Demanda\Model\DemandaPremiosSegmentos;
use Demanda\Model\DemandaRubros;
use Demanda\Model\DemandaSegmentos;
use Demanda\Model\Table\DemandaEmpresasAdicionalesTable;
use Demanda\Model\Table\DemandaEmpresasTable;
use Demanda\Model\Table\DemandaDepartamentosTable;
use Demanda\Model\Table\DemandaLogTable;
use Demanda\Model\Table\DemandaPremiosDepartamentosTable;
use Demanda\Model\Table\DemandaPremiosEmpresasAdicionalesTable;
use Demanda\Model\Table\DemandaPremiosEmpresasTable;
use Demanda\Model\Table\DemandaPremiosLogTable;
use Demanda\Model\Table\DemandaPremiosRubrosTable;
use Demanda\Model\Table\DemandaPremiosSegmentosTable;
use Demanda\Model\Table\DemandaPremiosTable;
use Demanda\Model\Table\DemandaRubrosTable;
use Demanda\Model\Table\DemandaSegmentosTable;
use Demanda\Model\Table\DemandaTable;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\ServiceManager;
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
                'mail.transport' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    $transport = new Smtp();
                    $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
                    return $transport;
                },
                //Demanda
                'Demanda\Model\Table\DemandaTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaTableGateway');
                    $table = new DemandaTable($tableGateway);
                    return $table;
                },
                'DemandaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Demanda());
                    return new TableGateway('BNF2_Demanda', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Rubros
                'Demanda\Model\Table\DemandaRubrosTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaRubrosTableGateway');
                    $table = new DemandaRubrosTable($tableGateway);
                    return $table;
                },
                'DemandaRubrosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaRubros());
                    return new TableGateway('BNF2_Demanda_Rubros', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Empresas
                'Demanda\Model\Table\DemandaEmpresasTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaEmpresasTableGateway');
                    $table = new DemandaEmpresasTable($tableGateway);
                    return $table;
                },
                'DemandaEmpresasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaEmpresas());
                    return new TableGateway('BNF2_Demanda_Empresas', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Provincias
                'Demanda\Model\Table\DemandaDepartamentosTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaDepartamentosTableGateway');
                    $table = new DemandaDepartamentosTable($tableGateway);
                    return $table;
                },
                'DemandaDepartamentosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaDepartamentos());
                    return new TableGateway('BNF2_Demanda_Departamentos', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Empresas Adicionales
                'Demanda\Model\Table\DemandaEmpresasAdicionalesTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaEmpresasAdicionalesTableGateway');
                    $table = new DemandaEmpresasAdicionalesTable($tableGateway);
                    return $table;
                },
                'DemandaEmpresasAdicionalesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaEmpresasAdicionales());
                    return new TableGateway('BNF2_Demanda_EmpresasAdicionales', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Log
                'Demanda\Model\Table\DemandaLogTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaLogTableGateway');
                    $table = new DemandaLogTable($tableGateway);
                    return $table;
                },
                'DemandaLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaLog());
                    return new TableGateway('BNF2_Demanda_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Segmentos
                'Demanda\Model\Table\DemandaSegmentosTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaSegmentosTableGateway');
                    $table = new DemandaSegmentosTable($tableGateway);
                    return $table;
                },
                'DemandaSegmentosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaSegmentos());
                    return new TableGateway('BNF2_Demanda_Segmentos', $dbAdapter, null, $resultSetPrototype);
                },
                //DemandaPremios
                'Demanda\Model\Table\DemandaPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaPremiosTableGateway');
                    $table = new DemandaPremiosTable($tableGateway);
                    return $table;
                },
                'DemandaPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaPremios());
                    return new TableGateway('BNF3_Demanda', $dbAdapter, null, $resultSetPrototype);
                },
                //DemandaPremios Rubros
                'Demanda\Model\Table\DemandaPremiosRubrosTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaPremiosRubrosTableGateway');
                    $table = new DemandaPremiosRubrosTable($tableGateway);
                    return $table;
                },
                'DemandaPremiosRubrosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaPremiosRubros());
                    return new TableGateway('BNF3_Demanda_Rubros', $dbAdapter, null, $resultSetPrototype);
                },
                //DemandaPremios Empresas
                'Demanda\Model\Table\DemandaPremiosEmpresasTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaPremiosEmpresasTableGateway');
                    $table = new DemandaPremiosEmpresasTable($tableGateway);
                    return $table;
                },
                'DemandaPremiosEmpresasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaPremiosEmpresas());
                    return new TableGateway('BNF3_Demanda_Empresas', $dbAdapter, null, $resultSetPrototype);
                },
                //DemandaPremios Provincias
                'Demanda\Model\Table\DemandaPremiosDepartamentosTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaPremiosDepartamentosTableGateway');
                    $table = new DemandaPremiosDepartamentosTable($tableGateway);
                    return $table;
                },
                'DemandaPremiosDepartamentosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaPremiosDepartamentos());
                    return new TableGateway('BNF3_Demanda_Departamentos', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Empresas Adicionales
                'Demanda\Model\Table\DemandaPremiosEmpresasAdicionalesTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaPremiosEmpresasAdicionalesTableGateway');
                    $table = new DemandaPremiosEmpresasAdicionalesTable($tableGateway);
                    return $table;
                },
                'DemandaPremiosEmpresasAdicionalesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaPremiosEmpresasAdicionales());
                    return new TableGateway('BNF3_Demanda_EmpresasAdicionales', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Log
                'Demanda\Model\Table\DemandaPremiosLogTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaPremiosLogTableGateway');
                    $table = new DemandaPremiosLogTable($tableGateway);
                    return $table;
                },
                'DemandaPremiosLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaPremiosLog());
                    return new TableGateway('BNF3_Demanda_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //Demanda Segmentos
                'Demanda\Model\Table\DemandaPremiosSegmentosTable' => function ($sm) {
                    $tableGateway = $sm->get('DemandaPremiosSegmentosTableGateway');
                    $table = new DemandaPremiosSegmentosTable($tableGateway);
                    return $table;
                },
                'DemandaPremiosSegmentosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DemandaPremiosSegmentos());
                    return new TableGateway('BNF3_Demanda_Segmentos', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
