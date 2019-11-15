<?php
namespace Oferta;

use Oferta\Model\Busqueda;
use Oferta\Model\Formulario;
use Oferta\Model\FormularioLead;
use Oferta\Model\Oferta;
use Oferta\Model\OfertaAtributos;
use Oferta\Model\OfertaCuponCodigo;
use Oferta\Model\OfertaFormulario;
use Oferta\Model\Table\OfertaAtributosTable;
use Oferta\Model\Table\OfertaCuponCodigoTable;
use Oferta\Model\Table\TarjetasOfertaTable;
use Oferta\Model\Tarjetas;
use Oferta\Model\TarjetasOferta;
use Oferta\Model\TipoBeneficio;
use Oferta\Model\Imagen;
use Oferta\Model\OfertaCampaniaUbigeo;
use Oferta\Model\OfertaCategoriaUbigeo;
use Oferta\Model\OfertaEmpresaCliente;
use Oferta\Model\OfertaRubro;
use Oferta\Model\OfertaSegmento;
use Oferta\Model\OfertaUbigeo;
use Oferta\Model\OfertaSubgrupo;
use Oferta\Model\Table\BusquedaTable;
use Oferta\Model\Table\FormularioLeadTable;
use Oferta\Model\Table\FormularioTable;
use Oferta\Model\Table\OfertaFormularioTable;
use Oferta\Model\Table\OfertaSubgrupoTable;
use Oferta\Model\Table\OfertaTable;
use Oferta\Model\Table\TipoBeneficioTable;
use Oferta\Model\Table\ImagenTable;
use Oferta\Model\Table\OfertaCampaniaUbigeoTable;
use Oferta\Model\Table\OfertaCategoriaUbigeoTable;
use Oferta\Model\Table\OfertaRubroTable;
use Oferta\Model\Table\OfertaSegmentoTable;
use Oferta\Model\Table\OfertaUbigeoTable;
use Oferta\Model\Table\OfertaEmpresaClienteTable;
use Oferta\Model\Table\TarjetasTable;

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
                //Oferta
                'Oferta\Model\Table\OfertaTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaTableGateway');
                    $table = new OfertaTable($tableGateway);
                    return $table;
                },
                'OfertaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Oferta());
                    return new TableGateway('BNF_Oferta', $dbAdapter, null, $resultSetPrototype);
                },
                //Tipo Beneficio
                'Oferta\Model\Table\TipoBeneficioTable' => function ($sm) {
                    $tableGateway = $sm->get('TipoBeneficioTableGateway');
                    $table = new TipoBeneficioTable($tableGateway);
                    return $table;
                },
                'TipoBeneficioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TipoBeneficio());
                    return new TableGateway('BNF_TipoBeneficio', $dbAdapter, null, $resultSetPrototype);
                },
                //Imagen
                'Oferta\Model\Table\ImagenTable' => function ($sm) {
                    $tableGateway = $sm->get('ImagenTableGateway');
                    $table = new ImagenTable($tableGateway);
                    return $table;
                },
                'ImagenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Imagen());
                    return new TableGateway('BNF_Imagen', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Ubigeo
                'Oferta\Model\Table\OfertaUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaUbigeoTableGateway');
                    $table = new OfertaUbigeoTable($tableGateway);
                    return $table;
                },
                'OfertaUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaUbigeo());
                    return new TableGateway('BNF_OfertaUbigeo', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Rubro
                'Oferta\Model\Table\OfertaRubroTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaRubroTableGateway');
                    $table = new OfertaRubroTable($tableGateway);
                    return $table;
                },
                'OfertaRubroTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaRubro());
                    return new TableGateway('BNF_OfertaRubro', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Segmento
                'Oferta\Model\Table\OfertaSegmentoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaSegmentoTableGateway');
                    $table = new OfertaSegmentoTable($tableGateway);
                    return $table;
                },
                'OfertaSegmentoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaSegmento());
                    return new TableGateway('BNF_OfertaSegmento', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Subgrupo
                'Oferta\Model\Table\OfertaSubgrupoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaSubgrupoTableGateway');
                    $table = new OfertaSubgrupoTable($tableGateway);
                    return $table;
                },
                'OfertaSubgrupoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaSubgrupo());
                    return new TableGateway('BNF_OfertaSubgrupo', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Categoria Ubigeo
                'Oferta\Model\Table\OfertaCategoriaUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaCategoriaUbigeoTableGateway');
                    $table = new OfertaCategoriaUbigeoTable($tableGateway);
                    return $table;
                },
                'OfertaCategoriaUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaCategoriaUbigeo());
                    return new TableGateway('BNF_OfertaCategoriaUbigeo', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Campaña Ubigeo
                'Oferta\Model\Table\OfertaCampaniaUbigeoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaCampaniaUbigeoTableGateway');
                    $table = new OfertaCampaniaUbigeoTable($tableGateway);
                    return $table;
                },
                'OfertaCampaniaUbigeoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaCampaniaUbigeo());
                    return new TableGateway('BNF_OfertaCampaniaUbigeo', $dbAdapter, null, $resultSetPrototype);
                },
                //Oferta Empresa Cliente
                'Oferta\Model\Table\OfertaEmpresaClienteTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaEmpresaClienteTableGateway');
                    $table = new OfertaEmpresaClienteTable($tableGateway);
                    return $table;
                },
                'OfertaEmpresaClienteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaEmpresaCliente());
                    return new TableGateway('BNF_OfertaEmpresaCliente', $dbAdapter, null, $resultSetPrototype);
                },
                //Busqueda
                'Oferta\Model\Table\BusquedaTable' => function ($sm) {
                    $tableGateway = $sm->get('BusquedaTableGateway');
                    $table = new BusquedaTable($tableGateway);
                    return $table;
                },
                'BusquedaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Busqueda());
                    return new TableGateway('BNF_Busqueda', $dbAdapter, null, $resultSetPrototype);
                },
                //Formulario
                'Oferta\Model\Table\FormularioTable' => function ($sm) {
                    $tableGateway = $sm->get('FormularioTableGateway');
                    $table = new FormularioTable($tableGateway);
                    return $table;
                },
                'FormularioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Formulario());
                    return new TableGateway('BNF_Formulario', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaFormulario
                'Oferta\Model\Table\OfertaFormularioTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaFormularioTableGateway');
                    $table = new OfertaFormularioTable($tableGateway);
                    return $table;
                },
                'OfertaFormularioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaFormulario());
                    return new TableGateway('BNF_OfertaFormulario', $dbAdapter, null, $resultSetPrototype);
                },
                //FormularioLead
                'Oferta\Model\Table\FormularioLeadTable' => function ($sm) {
                    $tableGateway = $sm->get('FormularioLeadTableGateway');
                    $table = new FormularioLeadTable($tableGateway);
                    return $table;
                },
                'FormularioLeadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FormularioLead());
                    return new TableGateway('BNF_FormularioLead', $dbAdapter, null, $resultSetPrototype);
                },
                //Tarjetas
                'Oferta\Model\Table\TarjetasTable' => function ($sm) {
                    $tableGateway = $sm->get('TarjetasTableGateway');
                    $table = new TarjetasTable($tableGateway);
                    return $table;
                },
                'TarjetasTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Tarjetas());
                    return new TableGateway('BNF_Tarjetas', $dbAdapter, null, $resultSetPrototype);
                },
                //TarjetasOferta
                'Oferta\Model\Table\TarjetasOfertaTable' => function ($sm) {
                    $tableGateway = $sm->get('TarjetasOfertaTableGateway');
                    $table = new TarjetasOfertaTable($tableGateway);
                    return $table;
                },
                'TarjetasOfertaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TarjetasOferta());
                    return new TableGateway('BNF_Tarjetas_Oferta', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaAtributos
                'Oferta\Model\Table\OfertaAtributosTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaAtributosTableGateway');
                    $table = new OfertaAtributosTable($tableGateway);
                    return $table;
                },
                'OfertaAtributosTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaAtributos());
                    return new TableGateway('BNF_Oferta_Atributos', $dbAdapter, null, $resultSetPrototype);
                },
                //OfertaCuponCodigo
                'Oferta\Model\Table\OfertaCuponCodigoTable' => function ($sm) {
                    $tableGateway = $sm->get('OfertaCuponCodigoTableGateway');
                    $table = new OfertaCuponCodigoTable($tableGateway);
                    return $table;
                },
                'OfertaCuponCodigoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OfertaCuponCodigo());
                    return new TableGateway('BNF_Oferta_Cupon_Codigo', $dbAdapter, null, $resultSetPrototype);
                },
            )
        );
    }
}
