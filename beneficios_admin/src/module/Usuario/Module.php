<?php
namespace Usuario;

use Usuario\Model\TipoDocumento;
use Usuario\Model\TipoDocumentoTable;
use Usuario\Model\Usuario;
use Usuario\Model\Table\UsuarioTable;
use Usuario\Model\TipoUsuario;
use Usuario\Model\TipoUsuarioTable;
use Usuario\Model\Segmento;
use Usuario\Model\SegmentoTable;
use Usuario\Model\SubGrupo;
use Usuario\Model\SubGrupoTable;

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
                //Usuario
                'Usuario\Model\Table\UsuarioTable' => function ($sm) {
                    $tableGateway = $sm->get('UsuarioTableGateway');
                    $table = new UsuarioTable($tableGateway);
                    return $table;
                },
                'UsuarioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Usuario());
                    return new TableGateway('BNF_Usuario', $dbAdapter, null, $resultSetPrototype);
                },
                //TipoUsuario
                'Usuario\Model\TipoUsuarioTable' => function ($sm) {
                    $tableGateway = $sm->get('TipoUsuarioTableGateway');
                    $table = new TipoUsuarioTable($tableGateway);
                    return $table;
                },
                'TipoUsuarioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TipoUsuario());
                    return new TableGateway('BNF_TipoUsuario', $dbAdapter, null, $resultSetPrototype);
                },
                //TipoDocumentos
                'Usuario\Model\TipoDocumentoTable' => function ($sm) {
                    $tableGateway = $sm->get('TipoDocumentoTableGateway');
                    $table = new TipoDocumentoTable($tableGateway);
                    return $table;
                },
                'TipoDocumentoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TipoDocumento());
                    return new TableGateway('BNF_TipoDocumento', $dbAdapter, null, $resultSetPrototype);
                },
                //Segmento
                'Usuario\Model\SegmentoTable' => function ($sm) {
                    $tableGateway = $sm->get('SegmentoTableGateway');
                    $table = new SegmentoTable($tableGateway);
                    return $table;
                },
                'SegmentoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Segmento());
                    return new TableGateway('BNF_Segmento', $dbAdapter, null, $resultSetPrototype);
                },
                //SubGrupo
                'Usuario\Model\SubGrupoTable' => function ($sm) {
                    $tableGateway = $sm->get('SubGrupoTableGateway');
                    $table = new SubGrupoTable($tableGateway);
                    return $table;
                },
                'SubGrupoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SubGrupo());
                    return new TableGateway('BNF_Subgrupo', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
