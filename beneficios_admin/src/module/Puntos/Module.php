<?php
namespace Puntos;

use Puntos\Model\Asignacion;
use Puntos\Model\AsignacionEstadoLog;
use Puntos\Model\CampaniaPLog;
use Puntos\Model\CampaniasP;
use Puntos\Model\CampaniasPEmpresas;
use Puntos\Model\CuponPuntosAsignacion;
use Puntos\Model\DeliveryPuntos;
use Puntos\Model\OfertaPuntos;
use Puntos\Model\OfertaPuntosAtributos;
use Puntos\Model\OfertaPuntosCampania;
use Puntos\Model\OfertaPuntosCategoria;
use Puntos\Model\OfertaPuntosDelivery;
use Puntos\Model\OfertaPuntosImagen;
use Puntos\Model\OfertaPuntosRubro;
use Puntos\Model\OfertaPuntosSegmento;
use Puntos\Model\OfertaPuntosUbigeo;
use Puntos\Model\SegmentosP;
use Puntos\Model\SegmentosPLog;
use Puntos\Model\Table\AsignacionEstadoLogTable;
use Puntos\Model\Table\AsignacionTable;
use Puntos\Model\Table\CampaniaPLogTable;
use Puntos\Model\Table\CampaniasPEmpresasTable;
use Puntos\Model\Table\CampaniasPTable;
use Puntos\Model\Table\CuponPuntosAsignacionTable;
use Puntos\Model\Table\DeliveryPuntosTable;
use Puntos\Model\Table\OfertaPuntosAtributosTable;
use Puntos\Model\Table\OfertaPuntosCampaniaTable;
use Puntos\Model\Table\OfertaPuntosCategoriaTable;
use Puntos\Model\Table\OfertaPuntosDeliveryTable;
use Puntos\Model\Table\OfertaPuntosImagenTable;
use Puntos\Model\Table\OfertaPuntosRubroTable;
use Puntos\Model\Table\OfertaPuntosSegmentoTable;
use Puntos\Model\Table\OfertaPuntosTable;
use Puntos\Model\Table\OfertaPuntosUbigeoTable;
use Puntos\Model\Table\SegmentosPLogTable;
use Puntos\Model\Table\SegmentosPTable;
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
                //Campanias Puntos
                'Puntos\Model\Table\CampaniasPTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniasPTableGateway');
                    $table = new CampaniasPTable($tableGateway);
                    return $table;
                },
                'CampaniasPTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CampaniasP());
                    return new TableGateway('BNF2_Campanias', $dbAdapter, null, $resultSetPrototype);
                },
                //Segmentos Puntos
                'Puntos\Model\Table\SegmentosPTable' => function ($sm) {
                    $tableGateway = $sm->get('SegmentosPTableGateway');
                    $table = new SegmentosPTable($tableGateway);
                    return $table;
                },
                'SegmentosPTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SegmentosP());
                    return new TableGateway('BNF2_Segmentos', $dbAdapter, null, $resultSetPrototype);
                },
                //Campanias Empresas Puntos
                'Puntos\Model\Table\CampaniasPEmpresasTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniasPEmpresasTableGateway');
                    $table = new CampaniasPEmpresasTable($tableGateway);
                    return $table;
                },
                'CampaniasPEmpresasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CampaniasPEmpresas());
                    return new TableGateway('BNF2_Campanias_Empresas', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos
                'Puntos\Model\Table\OfertaPuntosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosTableGateway');
                    $table = new OfertaPuntosTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntos());
                    return new TableGateway('BNF2_Oferta_Puntos', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos Atributos
                'Puntos\Model\Table\OfertaPuntosAtributosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosAtributosTableGateway');
                    $table = new OfertaPuntosAtributosTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosAtributosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosAtributos());
                    return new TableGateway('BNF2_Oferta_Puntos_Atributos', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos Campania
                'Puntos\Model\Table\OfertaPuntosCampaniaTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosCampaniaTableGateway');
                    $table = new OfertaPuntosCampaniaTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosCampaniaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosCampania());
                    return new TableGateway('BNF2_Oferta_Puntos_Campania', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos Categoria
                'Puntos\Model\Table\OfertaPuntosCategoriaTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosCategoriaTableGateway');
                    $table = new OfertaPuntosCategoriaTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosCategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosCategoria());
                    return new TableGateway('BNF2_Oferta_Puntos_Categoria', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos Imagen
                'Puntos\Model\Table\OfertaPuntosImagenTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosImagenTableGateway');
                    $table = new OfertaPuntosImagenTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosImagenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosImagen());
                    return new TableGateway('BNF2_Oferta_Puntos_Imagen', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos Rubro
                'Puntos\Model\Table\OfertaPuntosRubroTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosRubroTableGateway');
                    $table = new OfertaPuntosRubroTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosRubroTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosRubro());
                    return new TableGateway('BNF2_Oferta_Puntos_Rubro', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos Ubigeo
                'Puntos\Model\Table\OfertaPuntosUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosUbigeoTableGateway');
                    $table = new OfertaPuntosUbigeoTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosUbigeo());
                    return new TableGateway('BNF2_Oferta_Puntos_Ubigeo', $dbAdapter, null, $resultSetPrototype);
                },
                //Asignacion
                'Puntos\Model\Table\AsignacionTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionTableGateway');
                    $table = new AsignacionTable($tableGateway);
                    return $table;
                },
                'AsignacionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Asignacion());
                    return new TableGateway('BNF2_Asignacion_Puntos', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Puntos Segmentos
                'Puntos\Model\Table\OfertaPuntosSegmentoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosSegmentoTableGateway');
                    $table = new OfertaPuntosSegmentoTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosSegmentoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosSegmento());
                    return new TableGateway('BNF2_Oferta_Puntos_Segmentos', $dbAdapter, null, $resultSetPrototype);
                },
                //CampaniaLog
                'Puntos\Model\Table\CampaniaPLogTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniaPLogTableGateway');
                    $table = new CampaniaPLogTable($tableGateway);
                    return $table;
                },
                'CampaniaPLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CampaniaPLog());
                    return new TableGateway('BNF2_Campania_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //SegmentosLog
                'Puntos\Model\Table\SegmentosPLogTable' => function ($sm) {
                    $tableGateway = $sm->get('SegmentosPLogTableGateway');
                    $table = new SegmentosPLogTable($tableGateway);
                    return $table;
                },
                'SegmentosPLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SegmentosPLog());
                    return new TableGateway('BNF2_Segmentos_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //AsignacionEstadoLog
                'Puntos\Model\Table\AsignacionEstadoLogTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionEstadoLogTableGateway');
                    $table = new AsignacionEstadoLogTable($tableGateway);
                    return $table;
                },
                'AsignacionEstadoLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AsignacionEstadoLog());
                    return new TableGateway('BNF2_Asignacion_Puntos_Estado_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //CuponPuntosAsignacion
                'Puntos\Model\Table\CuponPuntosAsignacionTable' => function ($sm) {
                    $tableGateway = $sm->get('CuponPuntosAsignacionTableGateway');
                    $table = new CuponPuntosAsignacionTable($tableGateway);
                    return $table;
                },
                'CuponPuntosAsignacionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CuponPuntosAsignacion());
                    return new TableGateway('BNF2_Cupon_Puntos_Asignacion', $dbAdapter, null, $resultSetPrototype);
                },
                //DeliveryPuntos
                'Puntos\Model\Table\DeliveryPuntosTable' => function ($sm) {
                    $tableGateway = $sm->get('DeliveryPuntosTableGateway');
                    $table = new DeliveryPuntosTable($tableGateway);
                    return $table;
                },
                'DeliveryPuntosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DeliveryPuntos());
                    return new TableGateway('BNF2_Delivery_Puntos', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaDeliveryPuntos
                'Puntos\Model\Table\OfertaPuntosDeliveryTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPuntosDeliveryTableGateway');
                    $table = new OfertaPuntosDeliveryTable($tableGateway);
                    return $table;
                },
                'OfertaPuntosDeliveryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPuntosDelivery());
                    return new TableGateway('BNF2_Oferta_Puntos_Delivery', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
