<?php
namespace Ordenamiento;

use Ordenamiento\Model\Banner;
use Ordenamiento\Model\BannersCategoria;
use Ordenamiento\Model\BannersCampanias;
use Ordenamiento\Model\BannersTienda;
use Ordenamiento\Model\Galeria;
use Ordenamiento\Model\LayoutCampania;
use Ordenamiento\Model\LayoutCampaniaPosicion;
use Ordenamiento\Model\LayoutCategoria;
use Ordenamiento\Model\LayoutCategoriaPosicion;
use Ordenamiento\Model\LayoutPremios;
use Ordenamiento\Model\LayoutPremiosPosicion;
use Ordenamiento\Model\LayoutPuntos;
use Ordenamiento\Model\LayoutPuntosPosicion;
use Ordenamiento\Model\LayoutTienda;
use Ordenamiento\Model\LayoutTiendaPosicion;
use Ordenamiento\Model\Ordenamiento;
use Ordenamiento\Model\Table\BannersTiendaTable;
use Ordenamiento\Model\Table\BannerTable;
use Ordenamiento\Model\Table\BannersCampaniasTable;
use Ordenamiento\Model\Table\BannersCategoriaTable;
use Ordenamiento\Model\Table\GaleriaTable;
use Ordenamiento\Model\Table\LayoutCampaniaPosicionTable;
use Ordenamiento\Model\Table\LayoutCampaniaTable;
use Ordenamiento\Model\Table\LayoutCategoriaPosicionTable;
use Ordenamiento\Model\Table\LayoutCategoriaTable;
use Ordenamiento\Model\Table\LayoutPremiosPosicionTable;
use Ordenamiento\Model\Table\LayoutPremiosTable;
use Ordenamiento\Model\Table\LayoutPuntosPosicionTable;
use Ordenamiento\Model\Table\LayoutPuntosTable;
use Ordenamiento\Model\Table\LayoutTiendaPosicionTable;
use Ordenamiento\Model\Table\LayoutTiendaTable;
use Ordenamiento\Model\Table\OrdenamientoTable;

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
                //Ordenamiento
                'Ordenamiento\Model\Table\OrdenamientoTable' => function ($sm) {
                    $tableGateway = $sm->get('OrdenamientoTableGateway');
                    $table = new OrdenamientoTable($tableGateway);
                    return $table;
                },
                'OrdenamientoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Ordenamiento());
                    return new TableGateway('BNF_Layout', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutCategoria
                'Ordenamiento\Model\Table\LayoutCategoriaTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCategoriaTableGateway');
                    $table = new LayoutCategoriaTable($tableGateway);
                    return $table;
                },
                'LayoutCategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCategoria());
                    return new TableGateway('BNF_LayoutCategoria', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutCampania
                'Ordenamiento\Model\Table\LayoutCampaniaTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCampaniaTableGateway');
                    $table = new LayoutCampaniaTable($tableGateway);
                    return $table;
                },
                'LayoutCampaniaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCampania());
                    return new TableGateway('BNF_LayoutCampania', $dbAdapter, null, $resultSetPrototype);
                },
                //Banner
                'Ordenamiento\Model\Table\BannerTable' => function ($sm) {
                    $tableGateway = $sm->get('BannerTableGateway');
                    $table = new BannerTable($tableGateway);
                    return $table;
                },
                'BannerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Banner());
                    return new TableGateway('BNF_Banners', $dbAdapter, null, $resultSetPrototype);
                },
                //Galeria
                'Ordenamiento\Model\Table\GaleriaTable' => function ($sm) {
                    $tableGateway = $sm->get('GaleriaTableGateway');
                    $table = new GaleriaTable($tableGateway);
                    return $table;
                },
                'GaleriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Galeria());
                    return new TableGateway('BNF_Galeria', $dbAdapter, null, $resultSetPrototype);
                },
                //BannerCampanias
                'Ordenamiento\Model\Table\BannersCampaniasTable' => function ($sm) {
                    $tableGateway = $sm->get('BannersCampaniasTableGateway');
                    $table = new BannersCampaniasTable($tableGateway);
                    return $table;
                },
                'BannersCampaniasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BannersCampanias());
                    return new TableGateway('BNF_BannersCampanias', $dbAdapter, null, $resultSetPrototype);
                },
                //BannerCategoria
                'Ordenamiento\Model\Table\BannersCategoriaTable' => function ($sm) {
                    $tableGateway = $sm->get('BannersCategoriaTableGateway');
                    $table = new BannersCategoriaTable($tableGateway);
                    return $table;
                },
                'BannersCategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BannersCategoria());
                    return new TableGateway('BNF_BannersCategoria', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutTienda
                'Ordenamiento\Model\Table\LayoutTiendaTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutTiendaTableGateway');
                    $table = new LayoutTiendaTable($tableGateway);
                    return $table;
                },
                'LayoutTiendaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutTienda());
                    return new TableGateway('BNF_LayoutTienda', $dbAdapter, null, $resultSetPrototype);
                },
                //BannerTienda
                'Ordenamiento\Model\Table\BannersTiendaTable' => function ($sm) {
                    $tableGateway = $sm->get('BannersTiendaTableGateway');
                    $table = new BannersTiendaTable($tableGateway);
                    return $table;
                },
                'BannersTiendaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BannersTienda());
                    return new TableGateway('BNF_BannersTienda', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutCategoriaPosicion
                'Ordenamiento\Model\Table\LayoutCategoriaPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCategoriaPosicionTableGateway');
                    $table = new LayoutCategoriaPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutCategoriaPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCategoriaPosicion());
                    return new TableGateway('BNF_LayoutCategoriaPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutCampaniaPosicion
                'Ordenamiento\Model\Table\LayoutCampaniaPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutCampaniaPosicionTableGateway');
                    $table = new LayoutCampaniaPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutCampaniaPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutCampaniaPosicion());
                    return new TableGateway('BNF_LayoutCampaniaPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutTiendaPosicion
                'Ordenamiento\Model\Table\LayoutTiendaPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutTiendaPosicionTableGateway');
                    $table = new LayoutTiendaPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutTiendaPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutTiendaPosicion());
                    return new TableGateway('BNF_LayoutTiendaPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutPuntos
                'Ordenamiento\Model\Table\LayoutPuntosTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPuntosTableGateway');
                    $table = new LayoutPuntosTable($tableGateway);
                    return $table;
                },
                'LayoutPuntosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPuntos());
                    return new TableGateway('BNF_LayoutPuntos', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutPuntosPosicion
                'Ordenamiento\Model\Table\LayoutPuntosPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPuntosPosicionTableGateway');
                    $table = new LayoutPuntosPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutPuntosPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPuntosPosicion());
                    return new TableGateway('BNF_LayoutPuntosPosicion', $dbAdapter, null, $resultSetPrototype);
                },
                //Layout Premios
                'Ordenamiento\Model\Table\LayoutPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPremiosTableGateway');
                    $table = new LayoutPremiosTable($tableGateway);
                    return $table;
                },
                'LayoutPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPremios());
                    return new TableGateway('BNF_LayoutPremios', $dbAdapter, null, $resultSetPrototype);
                },
                //LayoutPremiosPosicion
                'Ordenamiento\Model\Table\LayoutPremiosPosicionTable' => function ($sm) {
                    $tableGateway = $sm->get('LayoutPremiosPosicionTableGateway');
                    $table = new LayoutPremiosPosicionTable($tableGateway);
                    return $table;
                },
                'LayoutPremiosPosicionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LayoutPremiosPosicion());
                    return new TableGateway('BNF_LayoutPremiosPosicion', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
