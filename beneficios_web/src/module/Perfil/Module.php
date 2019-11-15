<?php
namespace Perfil;

use Perfil\Model\CuponPuntos;
use Perfil\Model\Table\CuponPuntosTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Application\Service\MobileDetect;

class Module
{
    private $layout;

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
                //CuponPuntos
                'Perfil\Model\Table\CuponPuntosTable' => function ($sm) {
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
            )
        );
    }

    public function init(ModuleManager $mm)
    {
        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $this->layout = 'mobile/layout';
        } else {
            $this->layout = 'layout/layout';
        }

        $mm->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__,
            'dispatch',
            function ($e) {
                $e->getTarget()->layout($this->layout);
            }
        );
    }
}
