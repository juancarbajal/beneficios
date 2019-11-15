<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:51 PM
 */

namespace Application\Model\Table;

use Application\Model\OfertaPuntos;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getAllOfertaPuntos($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF2_Segmento_id" => $id));
        return $resultSet;
    }

    public function getOfertaPuntos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPuntosBySegmento($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF2_Segmento_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getIfExist($id)
    {
        $id = (int)$id;
        try {
            $rowset = $this->tableGateway->select(array('id' => $id));
            $row = $rowset->current();
            if (!$row) {
                throw new \Exception("Could not find row $id");
            }
            return $row;
        } catch (\Exception $ex) {
            return 0;
        }
    }

    public function getDetails($order_by, $order, $empresa = "", $titulo = null)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(array('id', 'Titulo', 'TipoPrecio', 'Estado'));
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($titulo)) {
            $select->where->equalTo("BNF2_Oferta_Puntos.id", $titulo);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF2_Oferta_Puntos.FechaCreacion DESC");
        }

        $select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getEmpresaClientes()
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('id', "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)"))
        );

        $select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        $select->order("Empresa ASC");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function deleteOfertaPuntos($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }



    public function totalOfertasByEmpresas($empresa)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(array('TotalOfertas' => new Expression('count(*)')));
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id',
            array(),
            'left'
        );

        $select->join(
            array('EP' => 'BNF_Empresa'),
            'BNF2_Oferta_Puntos.BNF_Empresa_id = EP.id',
            array('SlugEmpresa' => 'Slug')
        );

        $select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        $select->where->equalTo('BNF2_Oferta_Puntos.Estado', 'Publicado')
            ->AND->equalTo('BNF_Empresa.id', $empresa)
            ->AND->literal('IFNULL(BNF2_Oferta_Puntos.Stock, BNF2_Oferta_Puntos_Atributos.Stock) > 0')
            ->AND->literal('IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaInicio), 1) <= 0')
            ->AND->literal('IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaFin), 1) >= 0');

        $select->group('EP.id');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOrdenamientoOfertas(
        $ubigeo,
        $empresa,
        $categoria = 9,
        $campania = null,
        $segmentos = null,
        $offset = -1,
        $option = 0,
        $data = null
    )
    {
        $select = new Select();
        $select->from(array('OP' => 'BNF2_Oferta_Puntos'));
        $select->columns(
            array(
                'id',
                'TituloCorto',
                'Premium',
                'Direccion',
                'Telefono',
                'Slug',
                'PrecioVentaPublico',
                'TipoPrecio',
                'LogoEmpresa' => new Expression('EP.Logo'),
                'checkboxMoney' => new Expression('EP.checkboxMoney'),
                'ImagenOferta' => new Expression('IMG.Nombre'),
                'Empresa' => new Expression('EP.NombreComercial'),
                'TotalCupones' => new Expression(
                    "(SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS C
                    WHERE C.BNF2_Oferta_Puntos_id = OP.id  AND C.FechaGenerado IS NOT NULL)"
                ),
                'FechaVigencia' => new Expression(
                    '(IFNULL(OP.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia))'
                ),
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'OP.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            array('EC' => 'BNF_Empresa'),
            'BNF2_Campanias_Empresas.BNF_Empresa_id = EC.id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id',
            array(),
            'left'
        );

        //Imagen Oferta
        $select->join(
            array('IMG' => 'BNF2_Oferta_Puntos_Imagen'),
            'IMG.BNF2_Oferta_Puntos_id = OP.id',
            array()
        );
        //Empresa Proveedor
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'OP.BNF_Empresa_id = EP.id',
            array(
                'SlugEmpresa' => 'Slug'
            )
        );
        //Categoria Ubigeo
        $select->join(
            array('OCU' => 'BNF2_Oferta_Puntos_Categoria'),
            'OP.id = OCU.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            array('CU' => 'BNF_CategoriaUbigeo'),
            'CU.id = OCU.BNF_CategoriaUbigeo_id',
            array()
        );

        $select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        if (!empty($segmentos)) {
            $select->where->in("BNF2_Segmentos.id", $segmentos);
        }

        $select->where->equalTo('OP.Estado', 'Publicado')
            ->AND->equalTo('EC.id', $empresa)
            ->AND->literal('IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF2_Oferta_Puntos_Atributos WHERE BNF2_Oferta_Puntos_id = OP.id)) >= 1')
            ->AND->literal('IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaInicio), 1) <= 0')
            ->AND->literal('IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaFin), 1) >= 0')
            ->AND->equalTo('EC.Cliente', 1)
            ->AND->equalTo('EP.Proveedor ', 1)
            ->AND->equalTo('IMG.Principal', '1');

        if (!empty($ubigeo)) {
            //Oferta Ubigeo
            $select->join(
                array('OU' => 'BNF2_Oferta_Puntos_Ubigeo'),
                'OP.id = OU.BNF2_Oferta_Puntos_id',
                array()
            );

            $select->where->equalTo('OU.Eliminado', 0)
                ->AND->equalTo('OU.BNF_Ubigeo_id', $ubigeo);
        }

        if ($data != null) {
            $select->where->notIn('OP.id', $data);
        }
        //Filtra por Campaña
        if ($campania != null) {
            $select->where->equalTo('CNU.BNF_Campanias_id', $campania);
        }
        //Filtra por Categoria
        if ($categoria != null) {
            $select->where->equalTo('CU.BNF_Categoria_id', $categoria);
        }

        $select->group('OP.id');

        if ($option == 0) {
            $select->order('OP.Premium DESC');
            $select->order('OP.FechaCreacion DESC');
            $select->order('TotalCupones DESC');
        } elseif ($option == 1) {
            $select->where->equalTo('OP.Premium', 1);
            $select->order('OP.Premium DESC');
        } elseif ($option == 2) {
            $select->order('OP.FechaCreacion DESC');
            $select->limit(5);
        } elseif ($option == 3) {
            $select->order('TotalCupones DESC');
        }

        if ($offset > -1) {
            $select->limit(9);
            $select->offset($offset);
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    public function getCuponOferta($empresa, $dni, $slug = null, $segmentos = null)
    {
        $select = new Select();
        $select->from(array('OP' => 'BNF2_Oferta_Puntos'));
        $select->columns(
            array(
                'CaducadoTiempo' => new Expression(
                    'IFNULL(TIMESTAMPDIFF(DAY,CURDATE(),BNF2_Campanias.VigenciaFin),1)'
                ),
                'idOferta' => 'id',
                'TituloOferta' => 'Titulo',
                'CondicionesUso',
                'FechaVigencia' => new Expression(
                    '(IFNULL(OP.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia))'
                ),
                'Slug',
                'Direccion',
                'Telefono',
                'TipoPrecio',
                'Titulo',
                'PrecioVentaPublico',
                'PrecioBeneficio',
                'Estado'
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'OP.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            array('EC' => 'BNF_Empresa'),
            'BNF2_Campanias_Empresas.BNF_Empresa_id = EC.id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id',
            array(),
            'left'
        );

        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = OP.BNF_Empresa_id',
            array(
                'LogoEmpresa' => 'Logo',
                'Empresa' => 'NombreComercial',
                'SlugEmpresa' => 'Slug',
                'DescripcionEmpresa' => 'Descripcion',
                'DireccionEmpresa' => 'DireccionEmpresa',
                'TelefonoEmpresa' => 'Telefono',
                'WebEmpresa' => 'SitioWeb'
            )
        );

        if ($dni != null) {
            $select->join(
                array('ECC' => 'BNF_EmpresaClienteCliente'),
                'ECC.BNF_Empresa_id = EC.id',
                array()
            );
            $select->join(
                array('CLI' => 'BNF_Cliente'),
                'CLI.id = ECC.BNF_Cliente_id',
                array()
            );
        }

        //Oferta Ubigeo
        $select->join(
            array('OU' => 'BNF2_Oferta_Puntos_Ubigeo'),
            'OU.BNF2_Oferta_Puntos_id = OP.id',
            array()
        );
        $select->join(
            array('U' => 'BNF_Ubigeo'),
            'U.id = OU.BNF_Ubigeo_id',
            array()
        );
        //Categoria Ubigeo
        $select->join(
            array('OCU' => 'BNF2_Oferta_Puntos_Categoria'),
            'OCU.BNF2_Oferta_Puntos_id = OP.id',
            array()
        );
        $select->join(
            array('CU' => 'BNF_CategoriaUbigeo'),
            'CU.id = OCU.BNF_CategoriaUbigeo_id',
            array(
                'Categoria' => 'BNF_Categoria_id'
            )
        );

        $select->where->equalTo('EC.id', $empresa)
            ->AND->equalTo('EC.Cliente', 1)
            ->AND->equalTo('EP.Proveedor', 1)
            ->AND->equalTo('OU.Eliminado', 0)
            ->AND->equalTo('OP.Slug', $slug)
            ->AND->equalTo('OCU.Eliminado', 0);

        if ($dni != null) {
            $select->where->equalTo('CLI.NumeroDocumento', $dni);
        }

        $select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        if (!empty($segmentos)) {
            $select->where->in("BNF2_Segmentos.id", $segmentos);
        }
        $select->group('OP.id');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getCuponOfertaSlug($slug)
    {
        $select = new Select();
        $select->from(array('OP' => 'BNF2_Oferta_Puntos'));
        $select->columns(
            array(
                'CaducadoTiempo' => new Expression(
                    'IFNULL(TIMESTAMPDIFF(DAY,CURDATE(),BNF2_Campanias.VigenciaFin),1)'
                ),
                'idOferta' => 'id',
                'TituloOferta' => 'Titulo',
                'CondicionesUso',
                'FechaVigencia' => new Expression(
                    '(IFNULL(OP.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia))'
                ),
                'Slug',
                'Direccion',
                'Telefono',
                'TipoPrecio',
                'Titulo',
                'PrecioVentaPublico',
                'PrecioBeneficio',
                'Estado'
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'OP.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            array('EC' => 'BNF_Empresa'),
            'BNF2_Campanias_Empresas.BNF_Empresa_id = EC.id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id',
            array(),
            'left'
        );

        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = OP.BNF_Empresa_id',
            array(
                'LogoEmpresa' => 'Logo',
                'Empresa' => 'NombreComercial',
                'SlugEmpresa' => 'Slug',
                'DescripcionEmpresa' => 'Descripcion',
                'DireccionEmpresa' => 'DireccionEmpresa',
                'TelefonoEmpresa' => 'Telefono',
                'WebEmpresa' => 'SitioWeb'
            )
        );

        //Oferta Ubigeo
        $select->join(
            array('OU' => 'BNF2_Oferta_Puntos_Ubigeo'),
            'OU.BNF2_Oferta_Puntos_id = OP.id',
            array()
        );
        $select->join(
            array('U' => 'BNF_Ubigeo'),
            'U.id = OU.BNF_Ubigeo_id',
            array()
        );
        //Categoria Ubigeo
        $select->join(
            array('OCU' => 'BNF2_Oferta_Puntos_Categoria'),
            'OCU.BNF2_Oferta_Puntos_id = OP.id',
            array()
        );
        $select->join(
            array('CU' => 'BNF_CategoriaUbigeo'),
            'CU.id = OCU.BNF_CategoriaUbigeo_id',
            array(
                'Categoria' => 'BNF_Categoria_id'
            )
        );

        $select->where->equalTo('EC.Cliente', 1)
            ->AND->equalTo('EP.Proveedor', 1)
            ->AND->equalTo('OU.Eliminado', 0)
            ->AND->equalTo('OP.Slug', $slug)
            ->AND->equalTo('OCU.Eliminado', 0);

        $select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        if (!empty($segmentos)) {
            $select->where->in("BNF2_Segmentos.id", $segmentos);
        }
        $select->group('OP.id');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getImagenCupon($idOferta)
    {
        $id = (int)$idOferta;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->join(
            'BNF2_Oferta_Puntos_Imagen',
            'BNF2_Oferta_Puntos_Imagen.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array(
                'ImagenOferta' => 'Nombre',
                'Principal'
            )
        );
        $select->where
            ->equalTo('BNF2_Oferta_Puntos.id', $idOferta);
        $select->order('BNF2_Oferta_Puntos_Imagen.Principal DESC');

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertaDetalle($idOferta)
    {
        $select = new Select();
        $select->from(array('OP' => 'BNF2_Oferta_Puntos'));
        $select->columns(
            array(
                'LogoEmpresa' => new Expression('EP.Logo'),
                'nombreEmpresa' => new Expression('EP.NombreComercial'),
                'idCampania' => new Expression(
                    '(SELECT
                        CN.id
                    FROM
                        BNF_Campanias AS CN
                            INNER JOIN
                        BNF_CampaniaUbigeo AS CNU ON CN.id = CNU.BNF_Campanias_id
                    WHERE
                        CNU.Eliminado = 0
                            AND CN.Eliminado = 0
                    LIMIT 1)'
                ),
                'idOferta' => 'id',
                'TituloCorto' => 'TituloCorto',
                'Titulo' => 'Titulo',
                'CondicionesUso',
                'PrecioVentaPublico' => new Expression(
                    "(IF(OP.PrecioVentaPublico = 0, 
                    BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico, 
                    OP.PrecioVentaPublico))"
                ),
                'PrecioBeneficio' => new Expression(
                    "(IF(OP.PrecioBeneficio = 0, 
                        BNF2_Oferta_Puntos_Atributos.PrecioBeneficio, 
                        OP.PrecioBeneficio))"
                ),
                'Direccion',
                'Telefono',
                'FechaVigencia' => new Expression(
                    'IFNULL(OP.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia)'
                ),
                'Slug' => 'Slug',
                'DireccionOferta' => 'Direccion',
                'TelefonoOferta' => 'Telefono'
            )
        );

        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = OP.BNF_Empresa_id',
            array(
                'SlugEmpresa' => 'Slug',
                'DescripcionEmpresa' => 'Descripcion',
                'DireccionEmpresa' => 'DireccionEmpresa',
                'WebEmpresa' => 'SitioWeb',
                'TelefonoEmpresa' => 'Telefono',
                'DiasAtencionContacto' => 'HoraAtencionContacto',
                'HoraInicioContacto' => 'HoraAtencionInicioContacto',
                'HoraFinContacto' => 'HoraAtencionFinContacto',
                'EmailEmpresa' => 'CorreoPersonaAtencion',
                'Empresa' => 'NombreComercial',
                'NombreContacto' => 'NombreContacto',
                'TelefonoContacto' => 'TelefonoContacto',
                'CorreoContacto' => 'CorreoContacto',
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id',
            array(),
            'left'
        );

        $select->where->equalTo('OP.id', $idOferta);
        $select->group('OP.id');

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function updateOferta($data, $id)
    {
        if ($this->getOfertaPuntos($id)) {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('La Oferta no existe');
        }
        return $id;
    }
}
