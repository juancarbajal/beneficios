<?php
namespace Premios;

use Premios\Model\AsignacionPremios;
use Premios\Model\AsignacionPremiosEstadoLog;
use Premios\Model\CampaniaPremiosLog;
use Premios\Model\CampaniasPremios;
use Premios\Model\CampaniasPremiosEmpresas;
use Premios\Model\OfertaPremios;
use Premios\Model\OfertaPremiosAtributos;
use Premios\Model\OfertaPremiosCampania;
use Premios\Model\OfertaPremiosCategoria;
use Premios\Model\OfertaPremiosImagen;
use Premios\Model\OfertaPremiosRubro;
use Premios\Model\OfertaPremiosSegmento;
use Premios\Model\OfertaPremiosUbigeo;
use Premios\Model\SegmentosPremios;
use Premios\Model\SegmentosPremiosLog;
use Premios\Model\Table\AsignacionPremiosEstadoLogTable;
use Premios\Model\Table\AsignacionPremiosTable;
use Premios\Model\Table\CampaniaPremiosLogTable;
use Premios\Model\Table\CampaniasPremiosEmpresasTable;
use Premios\Model\Table\CampaniasPremiosTable;
use Premios\Model\Table\OfertaPremiosAtributosTable;
use Premios\Model\Table\OfertaPremiosCampaniaTable;
use Premios\Model\Table\OfertaPremiosCategoriaTable;
use Premios\Model\Table\OfertaPremiosImagenTable;
use Premios\Model\Table\OfertaPremiosRubroTable;
use Premios\Model\Table\OfertaPremiosSegmentoTable;
use Premios\Model\Table\OfertaPremiosTable;
use Premios\Model\Table\OfertaPremiosUbigeoTable;
use Premios\Model\Table\SegmentosPremiosLogTable;
use Premios\Model\Table\SegmentosPremiosTable;
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
                //Campanias Premios
                'Premios\Model\Table\CampaniasPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniasPremiosTableGateway');
                    $table = new CampaniasPremiosTable($tableGateway);
                    return $table;
                },
                'CampaniasPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CampaniasPremios());
                    return new TableGateway('BNF3_Campanias', $dbAdapter, null, $resultSetPrototype);
                },
                //Segmentos Premios
                'Premios\Model\Table\SegmentosPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('SegmentosPremiosTableGateway');
                    $table = new SegmentosPremiosTable($tableGateway);
                    return $table;
                },
                'SegmentosPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SegmentosPremios());
                    return new TableGateway('BNF3_Segmentos', $dbAdapter, null, $resultSetPrototype);
                },
                //Campanias Empresas Premios
                'Premios\Model\Table\CampaniasPremiosEmpresasTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniasPremiosEmpresasTableGateway');
                    $table = new CampaniasPremiosEmpresasTable($tableGateway);
                    return $table;
                },
                'CampaniasPremiosEmpresasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CampaniasPremiosEmpresas());
                    return new TableGateway('BNF3_Campanias_Empresas', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios
                'Premios\Model\Table\OfertaPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosTableGateway');
                    $table = new OfertaPremiosTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremios());
                    return new TableGateway('BNF3_Oferta_Premios', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Atributos
                'Premios\Model\Table\OfertaPremiosAtributosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosAtributosTableGateway');
                    $table = new OfertaPremiosAtributosTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosAtributosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosAtributos());
                    return new TableGateway('BNF3_Oferta_Premios_Atributos', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Campania
                'Premios\Model\Table\OfertaPremiosCampaniaTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosCampaniaTableGateway');
                    $table = new OfertaPremiosCampaniaTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosCampaniaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosCampania());
                    return new TableGateway('BNF3_Oferta_Premios_Campania', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Categoria
                'Premios\Model\Table\OfertaPremiosCategoriaTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosCategoriaTableGateway');
                    $table = new OfertaPremiosCategoriaTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosCategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosCategoria());
                    return new TableGateway('BNF3_Oferta_Premios_Categoria', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Imagen
                'Premios\Model\Table\OfertaPremiosImagenTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosImagenTableGateway');
                    $table = new OfertaPremiosImagenTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosImagenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosImagen());
                    return new TableGateway('BNF3_Oferta_Premios_Imagen', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Rubro
                'Premios\Model\Table\OfertaPremiosRubroTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosRubroTableGateway');
                    $table = new OfertaPremiosRubroTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosRubroTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosRubro());
                    return new TableGateway('BNF3_Oferta_Premios_Rubro', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Ubigeo
                'Premios\Model\Table\OfertaPremiosUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosUbigeoTableGateway');
                    $table = new OfertaPremiosUbigeoTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosUbigeo());
                    return new TableGateway('BNF3_Oferta_Premios_Ubigeo', $dbAdapter, null, $resultSetPrototype);
                },
                //AsignacionPremios
                'Premios\Model\Table\AsignacionPremiosTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionPremiosTableGateway');
                    $table = new AsignacionPremiosTable($tableGateway);
                    return $table;
                },
                'AsignacionPremiosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AsignacionPremios());
                    return new TableGateway('BNF3_Asignacion_Premios', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Premios Segmentos
                'Premios\Model\Table\OfertaPremiosSegmentoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaPremiosSegmentoTableGateway');
                    $table = new OfertaPremiosSegmentoTable($tableGateway);
                    return $table;
                },
                'OfertaPremiosSegmentoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaPremiosSegmento());
                    return new TableGateway('BNF3_Oferta_Premios_Segmentos', $dbAdapter, null, $resultSetPrototype);
                },
                //CampaniaLog
                'Premios\Model\Table\CampaniaPremiosLogTable' => function ($sm) {
                    $tableGateway = $sm->get('CampaniaPremiosLogTableGateway');
                    $table = new CampaniaPremiosLogTable($tableGateway);
                    return $table;
                },
                'CampaniaPremiosLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CampaniaPremiosLog());
                    return new TableGateway('BNF3_Campania_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //SegmentosLog
                'Premios\Model\Table\SegmentosPremiosLogTable' => function ($sm) {
                    $tableGateway = $sm->get('SegmentosPremiosLogTableGateway');
                    $table = new SegmentosPremiosLogTable($tableGateway);
                    return $table;
                },
                'SegmentosPremiosLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SegmentosPremiosLog());
                    return new TableGateway('BNF3_Segmentos_Log', $dbAdapter, null, $resultSetPrototype);
                },
                //AsignacionPremiosEstadoLog
                'Premios\Model\Table\AsignacionPremiosEstadoLogTable' => function ($sm) {
                    $tableGateway = $sm->get('AsignacionPremiosEstadoLogTableGateway');
                    $table = new AsignacionPremiosEstadoLogTable($tableGateway);
                    return $table;
                },
                'AsignacionPremiosEstadoLogTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AsignacionPremiosEstadoLog());
                    return new TableGateway('BNF3_Asignacion_Premios_Estado_Log', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
