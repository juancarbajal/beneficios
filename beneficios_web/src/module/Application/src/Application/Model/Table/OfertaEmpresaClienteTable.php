<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 21/10/15
 * Time: 18:48
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaEmpresaClienteTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getOrdenamientoOfertas(
        $ubigeo,
        $empresa,
        $segmento = 0,
        $categoria = 1,
        $subgrupo = 0,
        $campania = null,
        $tienda = null,
        $offset = -1,
        $option = 0,
        $data = null
    )
    {
        $select = new Select();
        $select->from(array('OEC' => 'BNF_OfertaEmpresaCliente'));
        $select->columns(
            array(
                'LogoEmpresa' => new Expression('EP.Logo'),
//                'checkboxLogo' => new Expression('EP.checkboxLogo'),
                'imagenOferta' => new Expression('IMG.Nombre'),
                'nombreEmpresa' => new Expression('EP.NombreComercial'),
                'total' => new Expression(
                    "(SELECT
                            COUNT(*)
                        FROM
                            BNF_Cupon AS C
                        WHERE
                            C.BNF_Oferta_id = O.id
                                AND C.FechaGenerado IS NOT NULL)"
                )
            )
        );
        $select->join(
            array('O' => 'BNF_Oferta'),
            'O.id = OEC.BNF_Oferta_id',
            array(
                'idOferta' => 'id',
                'TituloCortoOferta' => 'TituloCorto',
                'vigencia' => new Expression('IFNULL(OA.FechaVigencia ,O.FechaFinVigencia)'),
                'datoBeneficio' => 'DatoBeneficio',
                'idTipoBeneficio' => 'BNF_TipoBeneficio_id',
                'SlugOferta' => 'Slug',
                'Premium' => 'Premium'
            )
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = O.BNF_BolsaTotal_Empresa_id',
            array(
                'SlugEmpresa' => 'Slug'
            )
        );
        //Segmento
        $select->join(
            array('OS' => 'BNF_OfertaSegmento'),
            "OS.BNF_Oferta_id = O.id",
            array()
        );
        //Subgrupo
        if ($subgrupo > 0) {
            $select->join(
                array('OSG' => 'BNF_OfertaSubgrupo'),
                "OSG.BNF_Oferta_id = O.id",
                array()
            );
        }
        //Oferta Ubigeo
        $select->join(
            array('OU' => 'BNF_OfertaUbigeo'),
            'OU.BNF_Oferta_id = O.id',
            array()
        );
        //Categoria Ubigeo
        $select->join(
            array('OCU' => 'BNF_OfertaCategoriaUbigeo'),
            'OCU.BNF_Oferta_id = O.id',
            array()
        );
        $select->join(
            array('CU' => 'BNF_CategoriaUbigeo'),
            'CU.id = OCU.BNF_CategoriaUbigeo_id',
            array()
        );
        //Campaña Ubigeo
        $select->join(
            array('OCNU' => 'BNF_OfertaCampaniaUbigeo'),
            'OCNU.BNF_Oferta_id = O.id',
            array(),
            'left'
        );
        $select->join(
            array('CNU' => 'BNF_CampaniaUbigeo'),
            'CNU.BNF_Campanias_id = OCNU.BNF_CampaniaUbigeo_id',
            array(),
            'left'
        );
        // imagen Oferta
        $select->join(
            array('IMG' => 'BNF_Imagen'),
            'IMG.BNF_Oferta_id = O.id',
            array()
        );
        //tipo de Beneficio
        $select->join(
            array('TBN' => 'BNF_TipoBeneficio'),
            'TBN.id = O.BNF_TipoBeneficio_id',
            array()
        );
        //Oferta Atributos
        $select->join(
            array('OA' => 'BNF_Oferta_Atributos'),
            'OA.BNF_Oferta_id = O.id',
            array(),
            'left'
        );

        $select->where->literal('IFNULL(OA.Stock, O.Stock) > 0')
            ->and->equalTo('O.Estado', 'Publicado')
            ->and->literal('IFNULL( Timestampdiff(day,CURDATE(),O.FechaFinPublicacion) ,1) >= 0 ')
            ->and->literal('Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0')
            ->and->equalTo('OEC.Eliminado', 0)
            ->and->equalTo('OEC.BNF_Empresa_id', $empresa)
            ->and->equalTo('EP.Proveedor', 1)
            ->and->equalTo('IMG.Principal', '1')
            ->and->equalTo('OU.Eliminado', 0)
            ->and->equalTo('OU.BNF_Ubigeo_id', $ubigeo);

        if ($data != null) {
            $select->where->notIn('O.id', $data);
        }
        //Filtra por Campaña
        if ($campania != null) {
            $select->where->equalTo('CNU.BNF_Campanias_id', $campania)
                ->and->equalTo('OCNU.Eliminado', 0)
                ->and->equalTo('CNU.Eliminado', 0)
                ->and->equalTo('CNU.BNF_Pais_id', 1);
        }
        //Filtra por Categoria
        if ($categoria != null) {
            $select->where->equalTo('CU.BNF_Categoria_id', $categoria)
                ->and->equalTo('CU.Eliminado', 0)
                ->and->equalTo('OCU.Eliminado', 0)
                ->and->equalTo('CU.BNF_Pais_id', 1);
        }
        //Filtra por Tienda (Empresa Proveedora)
        if ($tienda != null) {
            $select->where->equalTo('EP.id', $tienda);
        }
        //Filtro Segmiento
        if ($segmento > 0) {
            $select->where->equalTo('OS.Eliminado', 0)
                ->and->equalTo('OS.BNF_Segmento_id', $segmento);
        }
        //Filtro Subgrupos
        if ($subgrupo > 0) {
            $select->where->equalTo('OSG.Eliminado', 0)
                ->and->equalTo('OSG.BNF_Subgrupo_id', $subgrupo);
        }

        $select->group('O.id');

        if ($option == 0) {
            $select->order('O.Premium DESC');
            $select->order('O.FechaInicioPublicacion DESC');
            $select->order('total DESC');
            $select->order('O.id DESC');
        } elseif ($option == 1) {
            $select->where->equalTo('O.Premium', 1);
            $select->order('O.Premium DESC');
            $select->order('O.id DESC');
            //$select->limit(5);
        } elseif ($option == 2) {
            $select->order('O.FechaInicioPublicacion DESC');
            $select->order('O.id DESC');
            $select->limit(5);
        } elseif ($option == 3) {
            $select->order('total DESC');
            $select->order('O.id DESC');
            //$select->limit(5);
        }

        if ($offset > -1) {
            $select->limit(9);
            $select->offset($offset);
        }

        //echo str_replace('"','', $select->getSqlString()); exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getImagenOfertaXName(
        $name,
        $premium,
        $destacados,
        $novedades,
        $ubigeo,
        $empresa,
        $segmento,
        $subgrupo = 0,
        $offset = 0,
        $segmentos,
        $puntos,
        $premios,
        $segmentos_premios
    )
    {
        #region Consulta Ofertas
        $query_1 = "SELECT O.id AS idOferta, 
                    O.TituloCorto AS TituloCortoOferta,
                    IMG.Nombre AS imagenOferta, 
                    O.Slug AS SlugOferta, 
                    O.DatoBeneficio AS datoBeneficio,
                    O.BNF_TipoBeneficio_id AS idTipoBeneficio,
                    O.Premium AS Premium, 
                    ( IF(O.BNF_BolsaTotal_TipoPaquete_id = 3, IFNULL((SELECT COUNT(*) FROM BNF_OfertaFormCliente AS OFC 
                    WHERE OFC.BNF_Oferta_id = O.id GROUP BY OFC.BNF_Oferta_id), 0),
                    (SELECT COUNT(*) FROM BNF_Cupon AS C WHERE C.BNF_Oferta_id = O.id AND C.FechaGenerado IS NOT NULL)))
                      AS total,
                    O.FechaFinVigencia AS vigencia,
                    '' AS Direccion,
                    '' AS Telefono,
                    EP.Logo AS LogoEmpresa, 
                    EP.NombreComercial AS nombreEmpresa,     
                    EP.Slug AS SlugEmpresa,
                    1 as TipoOferta,
                    O.FechaInicioPublicacion AS FechaInicioPublicacion
                    FROM BNF_OfertaEmpresaCliente AS OEC 
                    INNER JOIN BNF_Oferta AS O ON O.id = OEC.BNF_Oferta_id 
                    LEFT JOIN BNF_Oferta_Atributos AS OA ON O.id = OA.BNF_Oferta_id 
                    INNER JOIN BNF_Empresa AS EP ON EP.id = O.BNF_BolsaTotal_Empresa_id 
                    INNER JOIN BNF_OfertaUbigeo AS OU ON OU.BNF_Oferta_id = O.id 
                    INNER JOIN BNF_Imagen AS IMG ON IMG.BNF_Oferta_id = O.id 
                    INNER JOIN BNF_TipoBeneficio AS TBN ON TBN.id = O.BNF_TipoBeneficio_id 
                    INNER JOIN BNF_Busqueda AS B ON O.id = B.BNF_Oferta_id ";

        if ($segmento > 0) {
            $query_1 .= "INNER JOIN BNF_OfertaSegmento AS OS ON OS.BNF_Oferta_id = O.id ";
        }

        if ($subgrupo > 0) {
            $query_1 .= "INNER JOIN BNF_OfertaSubgrupo AS OSG ON OSG.BNF_Oferta_id = O.id ";
        }

        $query_1 .= "WHERE O.Estado = 'Publicado' 
                    AND IFNULL(O.Stock, (SELECT SUM(Stock) From BNF_Oferta_Atributos WHERE BNF_Oferta_id = O.id)) > 0
                    AND IFNULL( Timestampdiff(day,CURDATE(), O.FechaFinPublicacion) ,1) >= 0
                    AND Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0 
                    AND OEC.Eliminado = '0' 
                    AND OEC.BNF_Empresa_id = $empresa
                    AND EP.Proveedor = 1 
                    AND IMG.Principal = '1' 
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 1
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";

        if ($segmento > 0) {
            $query_1 .= " AND OS.Eliminado = '0'  AND OS.BNF_Segmento_id = $segmento ";
        }

        if ($subgrupo > 0) {
            $query_1 .= " AND OSG.Eliminado = '0' AND OSG.BNF_Subgrupo_id = $subgrupo ";
        }

        $query_1 .= " GROUP BY idOferta ";
        #endregion

        #region Consulta Ofertas Puntos
        $query_2 = "SELECT OP.id AS idOferta, 
                    OP.TituloCorto AS TituloCortoOferta, 
                    IMG.Nombre AS imagenOferta,
                    OP.Slug AS SlugOferta, 
                    OP.PrecioVentaPublico AS datoBeneficio,  
                    '' AS idTipoBeneficio,
                    OP.Premium AS Premium, 
                    (SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS C WHERE C.BNF2_Oferta_Puntos_id = OP.id 
                    AND C.FechaGenerado IS NOT NULL) AS total,
                    (IFNULL(OP.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia)) AS vigencia,
                    OP.Direccion AS Direccion, 
                    OP.Telefono AS Telefono,
                    EP.Logo AS LogoEmpresa, 
                    EP.NombreComercial AS nombreEmpresa,   
                    EP.Slug AS SlugEmpresa,
                    2 as TipoOferta,
                    BNF2_Campanias.VigenciaInicio AS FechaInicioPublicacion
                    FROM BNF2_Oferta_Puntos AS OP 
                    INNER JOIN BNF2_Oferta_Puntos_Segmentos ON OP.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Segmentos ON BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id 
                    INNER JOIN BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id 
                    INNER JOIN BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF2_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF2_Oferta_Puntos_Atributos ON OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Imagen AS IMG ON IMG.BNF2_Oferta_Puntos_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF2_Oferta_Puntos_Ubigeo AS OU ON OP.id = OU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Categoria AS OCU ON OP.id = OCU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id 
                    INNER JOIN BNF_Busqueda AS B ON OP.id = B.BNF_Oferta_id
                    WHERE BNF2_Oferta_Puntos_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' 
                    AND EC.id = $empresa 
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF2_Oferta_Puntos_Atributos WHERE BNF2_Oferta_Puntos_id = OP.id)) > 0 
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaFin), 1) >= 1 
                    AND EC.Cliente = 1 
                    AND EP.Proveedor = 1 
                    AND IMG.Principal = '1' 
                    AND OU.Eliminado = 0 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 2
                    AND CU.BNF_Categoria_id = 9 
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";

        if (!empty($segmentos)) {
            $query_2 .= " AND BNF2_Segmentos.id IN (";
            $count = 0;
            foreach ($segmentos as $value) {
                if ($count == 0)
                    $query_2 .= $value;
                else
                    $query_2 .= ',' . $value;
                $count++;
            }
            $query_2 .= ") ";
        }

        $query_2 .= " GROUP BY idOferta ";
        #endregion

        #region Consulta Ofertas Premios
        $query_3 = "SELECT OP.id AS idOferta, 
                    OP.TituloCorto AS TituloCortoOferta, 
                    IMG.Nombre AS imagenOferta,
                    OP.Slug AS SlugOferta, 
                    OP.PrecioVentaPublico AS datoBeneficio,  
                    '' AS idTipoBeneficio,
                    OP.Premium AS Premium, 
                    (SELECT COUNT(*) FROM BNF3_Cupon_Premios AS C WHERE C.BNF3_Oferta_Premios_id = OP.id 
                    AND C.FechaGenerado IS NOT NULL) AS total,
                    (IFNULL(OP.FechaVigencia, BNF3_Oferta_Premios_Atributos.FechaVigencia)) AS vigencia,
                    OP.Direccion AS Direccion, 
                    OP.Telefono AS Telefono,
                    EP.Logo AS LogoEmpresa, 
                    EP.NombreComercial AS nombreEmpresa,   
                    EP.Slug AS SlugEmpresa,
                    3 as TipoOferta,
                    BNF3_Campanias.VigenciaInicio AS FechaInicioPublicacion
                    FROM BNF3_Oferta_Premios AS OP 
                    INNER JOIN BNF3_Oferta_Premios_Segmentos ON OP.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Segmentos ON BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id 
                    INNER JOIN BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id 
                    INNER JOIN BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF3_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF3_Oferta_Premios_Atributos ON OP.id = BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Imagen AS IMG ON IMG.BNF3_Oferta_Premios_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF3_Oferta_Premios_Ubigeo AS OU ON OP.id = OU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Categoria AS OCU ON OP.id = OCU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id  
                    INNER JOIN BNF_Busqueda AS B ON OP.id = B.BNF_Oferta_id
                    WHERE BNF3_Oferta_Premios_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' 
                    AND EC.id = $empresa 
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF3_Oferta_Premios_Atributos WHERE BNF3_Oferta_Premios_id = OP.id)) > 0 
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF3_Campanias.VigenciaFin), 1) >= 1 
                    AND EC.Cliente = 1 
                    AND EP.Proveedor = 1 
                    AND IMG.Principal = '1' 
                    AND OU.Eliminado = 0 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 3
                    AND CU.BNF_Categoria_id = 10
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";

        if (!empty($segmentos_premios)) {
            $query_3 .= " AND BNF3_Segmentos.id IN (";
            $count = 0;
            foreach ($segmentos_premios as $value) {
                if ($count == 0)
                    $query_3 .= $value;
                else
                    $query_3 .= ',' . $value;
                $count++;
            }
            $query_3 .= ") ";
        }

        $query_3 .= " GROUP BY idOferta ";
        #endregion

        #region Consulta Ofertas Empresas
        $query_4 = " SELECT O.id AS idOferta, 
                    O.TituloCorto AS TituloCortoOferta,
                    IMG.Nombre AS imagenOferta, 
                    O.Slug AS SlugOferta, 
                    O.DatoBeneficio AS datoBeneficio,
                    O.BNF_TipoBeneficio_id AS idTipoBeneficio,
                    O.Premium AS Premium, 
                    ( IF(O.BNF_BolsaTotal_TipoPaquete_id = 3, IFNULL((SELECT COUNT(*) FROM BNF_OfertaFormCliente AS OFC 
                    WHERE OFC.BNF_Oferta_id = O.id GROUP BY OFC.BNF_Oferta_id), 0),
                    (SELECT COUNT(*) FROM BNF_Cupon AS C WHERE C.BNF_Oferta_id = O.id AND C.FechaGenerado IS NOT NULL)))
                      AS total,
                    O.FechaFinVigencia AS vigencia,
                    '' AS Direccion,
                    '' AS Telefono,
                    EP.Logo AS LogoEmpresa, 
                    EP.NombreComercial AS nombreEmpresa,     
                    EP.Slug AS SlugEmpresa,
                    1 as TipoOferta,
                    O.FechaInicioPublicacion AS FechaInicioPublicacion
                    FROM BNF_OfertaEmpresaCliente AS OEC 
                    INNER JOIN BNF_Oferta AS O ON O.id = OEC.BNF_Oferta_id 
                    LEFT JOIN BNF_Oferta_Atributos ON O.id = BNF_Oferta_Atributos.BNF_Oferta_id 
                    INNER JOIN BNF_Empresa AS EP ON EP.id = O.BNF_BolsaTotal_Empresa_id 
                    INNER JOIN BNF_OfertaUbigeo AS OU ON OU.BNF_Oferta_id = O.id 
                    INNER JOIN BNF_Imagen AS IMG ON IMG.BNF_Oferta_id = O.id 
                    INNER JOIN BNF_TipoBeneficio AS TBN ON TBN.id = O.BNF_TipoBeneficio_id 
                    INNER JOIN BNF_Busqueda AS B ON EP.id = B.BNF_Oferta_id ";

        if ($segmento > 0) {
            $query_4 .= "INNER JOIN BNF_OfertaSegmento AS OS ON OS.BNF_Oferta_id = O.id ";
        }

        if ($subgrupo > 0) {
            $query_4 .= "INNER JOIN BNF_OfertaSubgrupo AS OSG ON OSG.BNF_Oferta_id = O.id ";
        }

        $query_4 .= "WHERE O.Estado = 'Publicado' 
                    AND IFNULL(O.Stock, (SELECT SUM(Stock) From BNF_Oferta_Atributos WHERE BNF_Oferta_id = O.id)) > 0
                    AND IFNULL( Timestampdiff(day,CURDATE(),O.FechaFinPublicacion) ,1) >= 0 
                    AND Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0 
                    AND OEC.Eliminado = '0' 
                    AND OEC.BNF_Empresa_id = $empresa
                    AND EP.Proveedor = 1 
                    AND IMG.Principal = '1' 
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 0
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";

        if ($segmento > 0) {
            $query_4 .= " AND OS.Eliminado = '0'  AND OS.BNF_Segmento_id = $segmento ";
        }

        if ($subgrupo > 0) {
            $query_4 .= " AND OSG.Eliminado = '0' AND OSG.BNF_Subgrupo_id = $subgrupo ";
        }

        $query_4 .= " GROUP BY idOferta ";
        #endregion

        #region Consulta Ofertas Puntos Empresas
        $query_5 = " SELECT OP.id AS idOferta, 
                    OP.TituloCorto AS TituloCortoOferta, 
                    IMG.Nombre AS imagenOferta,
                    OP.Slug AS SlugOferta, 
                    OP.PrecioVentaPublico AS datoBeneficio,  
                    '' AS idTipoBeneficio,
                    OP.Premium AS Premium, 
                    (SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS C WHERE C.BNF2_Oferta_Puntos_id = OP.id 
                    AND C.FechaGenerado IS NOT NULL) AS total,
                    (IFNULL(OP.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia)) AS vigencia,
                    OP.Direccion AS Direccion, 
                    OP.Telefono AS Telefono,
                    EP.Logo AS LogoEmpresa, 
                    EP.NombreComercial AS nombreEmpresa,   
                    EP.Slug AS SlugEmpresa,
                    2 as TipoOferta,
                    BNF2_Campanias.VigenciaInicio AS FechaInicioPublicacion
                    FROM BNF2_Oferta_Puntos AS OP 
                    INNER JOIN BNF2_Oferta_Puntos_Segmentos ON OP.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Segmentos ON BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id 
                    INNER JOIN BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id 
                    INNER JOIN BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF2_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF2_Oferta_Puntos_Atributos ON OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Imagen AS IMG ON IMG.BNF2_Oferta_Puntos_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF2_Oferta_Puntos_Ubigeo AS OU ON OP.id = OU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Categoria AS OCU ON OP.id = OCU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id 
                    INNER JOIN BNF_Busqueda AS B ON EP.id = B.BNF_Oferta_id
                    WHERE BNF2_Oferta_Puntos_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' 
                    AND EC.id = $empresa 
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF2_Oferta_Puntos_Atributos WHERE BNF2_Oferta_Puntos_id = OP.id)) > 0 
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaFin), 1) >= 1 
                    AND EC.Cliente = 1 
                    AND EP.Proveedor = 1 
                    AND IMG.Principal = '1' 
                    AND OU.Eliminado = 0 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 0
                    AND CU.BNF_Categoria_id = 9 
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";

        if (!empty($segmentos)) {
            $query_5 .= " AND BNF2_Segmentos.id IN (";
            $count = 0;
            foreach ($segmentos as $value) {
                if ($count == 0)
                    $query_5 .= $value;
                else
                    $query_5 .= ',' . $value;
                $count++;
            }
            $query_5 .= ") ";
        }

        $query_5 .= " GROUP BY idOferta ";
        #endregion

        #region Consulta Ofertas Premios Empresas
        $query_6 = " SELECT OP.id AS idOferta, 
                    OP.TituloCorto AS TituloCortoOferta, 
                    IMG.Nombre AS imagenOferta,
                    OP.Slug AS SlugOferta, 
                    OP.PrecioVentaPublico AS datoBeneficio,  
                    '' AS idTipoBeneficio,
                    OP.Premium AS Premium, 
                    (SELECT COUNT(*) FROM BNF3_Cupon_Premios AS C WHERE C.BNF3_Oferta_Premios_id = OP.id 
                    AND C.FechaGenerado IS NOT NULL) AS total,
                    (IFNULL(OP.FechaVigencia, BNF3_Oferta_Premios_Atributos.FechaVigencia)) AS vigencia,
                    OP.Direccion AS Direccion, 
                    OP.Telefono AS Telefono,
                    EP.Logo AS LogoEmpresa, 
                    EP.NombreComercial AS nombreEmpresa,   
                    EP.Slug AS SlugEmpresa,
                    3 as TipoOferta,
                    BNF3_Campanias.VigenciaInicio AS FechaInicioPublicacion
                    FROM BNF3_Oferta_Premios AS OP 
                    INNER JOIN BNF3_Oferta_Premios_Segmentos ON OP.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Segmentos ON BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id 
                    INNER JOIN BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id 
                    INNER JOIN BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF3_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF3_Oferta_Premios_Atributos ON OP.id = BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Imagen AS IMG ON IMG.BNF3_Oferta_Premios_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF3_Oferta_Premios_Ubigeo AS OU ON OP.id = OU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Categoria AS OCU ON OP.id = OCU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id  
                    INNER JOIN BNF_Busqueda AS B ON EP.id = B.BNF_Oferta_id
                    WHERE BNF3_Oferta_Premios_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' 
                    AND EC.id = $empresa 
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF3_Oferta_Premios_Atributos WHERE BNF3_Oferta_Premios_id = OP.id)) > 0  
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF3_Campanias.VigenciaFin), 1) >= 1 
                    AND EC.Cliente = 1 
                    AND EP.Proveedor = 1 
                    AND IMG.Principal = '1' 
                    AND OU.Eliminado = 0 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 0
                    AND CU.BNF_Categoria_id = 10 
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";

        if (!empty($segmentos_premios)) {
            $query_6 .= " AND BNF3_Segmentos.id IN (";
            $count = 0;
            foreach ($segmentos_premios as $value) {
                if ($count == 0)
                    $query_6 .= $value;
                else
                    $query_6 .= ',' . $value;
                $count++;
            }
            $query_6 .= ") ";
        }

        $query_6 .= " GROUP BY idOferta ";
        #endregion

        if (empty($puntos) && empty($premios)) {
            $query = $query_1 . " UNION " . $query_4;
        } elseif (!empty($puntos) && empty($premios)) {
            $query = $query_1 . " UNION " . $query_2 . " UNION " . $query_4 . " UNION " . $query_5;
        }elseif (empty($puntos) && !empty($premios)) {
            $query = $query_1 . " UNION " . $query_3 . " UNION " . $query_4 . " UNION " . $query_6;
        } else {
            $query = $query_1 . " UNION " . $query_2 . " UNION " . $query_3 . " UNION " . $query_4 . " UNION " .
                $query_5 . " UNION " . $query_6;
        }

        if ($premium == 0 && $destacados == 0 && $novedades == 0) {
            $query .= " ORDER BY Premium DESC, total DESC, idOferta DESC, FechaInicioPublicacion DESC ";
        } elseif ($premium == 1 && $destacados == 0 && $novedades == 0) {
            $query .= " ORDER BY Premium DESC, idOferta DESC,FechaInicioPublicacion DESC ";
        } elseif ($destacados == 1 && $premium == 0 && $novedades == 0) {
            $query .= " ORDER BY total DESC, FechaInicioPublicacion DESC, idOferta DESC ";
        } elseif ($novedades == 1 && $destacados == 0 && $premium == 0) {
            $query .= " ORDER BY FechaInicioPublicacion DESC, idOferta DESC ";
        }

        $query .= " LIMIT 9 OFFSET $offset";

        //echo str_replace('"','', $query); exit;

        $dbAdapter = $this->tableGateway->adapter->getDriver();
        $statement = $dbAdapter->createStatement($query);
        $resultSet = $statement->execute();
        $resultSet->buffer();
        return $resultSet;
    }

    public function getCuponOferta($empresa, $dni, $subgrupo = 0, $slug = null)
    {
        $select = new Select();
        $select->from(array('OEC' => 'BNF_OfertaEmpresaCliente'));
        $select->columns(
            array(
                'LogoEmpresa' => new Expression('EP.Logo'),
                'nombreEmpresa' => new Expression('EP.NombreComercial'),
                'caducadoTiempo' => new Expression(
                    'IFNULL(Timestampdiff(day,CURDATE(),O.FechaFinPublicacion),1)'
                ),
            )
        );

        $select->join(
            array('O' => 'BNF_Oferta'),
            'O.id = OEC.BNF_Oferta_id',
            array(
                'idOferta' => 'id',
                'TituloOferta' => 'Titulo',
                'condicionesUso' => 'CondicionesUso',
                'vigencia' => 'FechaFinVigencia',
                'datoBeneficio' => 'DatoBeneficio',
                'idTipoBeneficio' => 'BNF_TipoBeneficio_id',
                'SlugOferta' => 'Slug',
                'TipoOferta' => 'BNF_BolsaTotal_TipoPaquete_id',
                'DireccionOferta' => 'Direccion',
                'TelefonoOferta' => 'Telefono',
                'EstadoOferta' => 'Estado',
                'CondicionesTebca',
                'TipoAtributo',
                'Stock' => new Expression(
                    'IFNULL(O.Stock, (SELECT SUM(Stock) FROM BNF_Oferta_Atributos WHERE BNF_Oferta_id = O.id))')

            )
        );

        $select->join(
            array('OA' => 'BNF_Oferta_Atributos'),
            'O.id = OA.BNF_Oferta_id',
            array(),
            'left'
        );

        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = O.BNF_BolsaTotal_Empresa_id',
            array(
                'SlugEmpresa' => 'Slug',
                'DescripcionEmpresa' => 'Descripcion',
                'webEmpresa' => 'SitioWeb',
                'DireccionEmpresa' => 'DireccionEmpresa',
                'TelefonoEmpresa' => 'Telefono'
            )
        );

        if ($dni != null) {
            $select->join(
                array('ECC' => 'BNF_EmpresaClienteCliente'),
                'ECC.BNF_Empresa_id = OEC.BNF_Empresa_id',
                array()
            );
            $select->join(
                array('CLI' => 'BNF_Cliente'),
                'CLI.id = ECC.BNF_Cliente_id',
                array()
            );
            $select->join(
                array('OS' => 'BNF_OfertaSegmento'),
                "OS.BNF_Oferta_id = O.id",
                array()
            );
            $select->join(
                array('ES' => 'BNF_EmpresaSegmento'),
                'ES.BNF_Empresa_id = OEC.BNF_Empresa_id AND ES.BNF_Segmento_id = OS.BNF_Segmento_id',
                array()
            );
            $select->join(
                array('ESC' => 'BNF_EmpresaSegmentoCliente'),
                'ESC.BNF_EmpresaSegmento_id = ES.id AND ESC.BNF_Cliente_id = CLI.id',
                array()
            );
        }
        if ($subgrupo == 1) {
            // Subgrupo Cliente
            $select->join(
                array('OSG' => 'BNF_OfertaSubgrupo'),
                "OSG.BNF_Oferta_id = O.id",
                array()
            );
            $select->join(
                array('SG' => 'BNF_Subgrupo'),
                'SG.BNF_Empresa_id = OEC.BNF_Empresa_id AND SG.id = OSG.BNF_Subgrupo_id',
                array()
            );
            $select->join(
                array('ESGC' => 'BNF_EmpresaSubgrupoCliente'),
                "ESGC.BNF_Cliente_id = CLI.id AND ESGC.BNF_Subgrupo_id = SG.id",
                array()
            );
        }
        // ubigeo oferta
        $select->join(
            array('OU' => 'BNF_OfertaUbigeo'),
            'OU.BNF_Oferta_id = O.id',
            array()
        );
        $select->join(
            array('U' => 'BNF_Ubigeo'),
            'U.id = OU.BNF_Ubigeo_id',
            array()
        );
        $select->join(
            array('OCU' => 'BNF_OfertaCategoriaUbigeo'),
            'OCU.BNF_Oferta_id = O.id',
            array()
        );
        $select->join(
            array('CU' => 'BNF_CategoriaUbigeo'),
            'CU.id = OCU.BNF_CategoriaUbigeo_id AND CU.BNF_Pais_id = U.BNF_Pais_id',
            array(
                'idCategoria' => 'BNF_Categoria_id'
            )
        );
        // Campaña ubigeo
        $select->join(
            array('OCNU' => 'BNF_OfertaCampaniaUbigeo'),
            'OCNU.BNF_Oferta_id = O.id',
            array(),
            'left'
        );
        $select->join(
            array('CNU' => 'BNF_CampaniaUbigeo'),
            'CNU.BNF_Campanias_id = OCNU.BNF_CampaniaUbigeo_id AND CNU.BNF_Pais_id = U.BNF_Pais_id',
            array(),
            'left'
        );

        $select->where
            ->literal('Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0')
            ->and
            ->equalTo('OEC.Eliminado', 0)
            ->and
            ->equalTo('EP.Proveedor', 1)
            ->and
            ->equalTo('OU.Eliminado', 0)
            ->and
            ->equalTo('OEC.BNF_Empresa_id', $empresa)
            ->and
            ->equalTo('O.Slug', $slug)
            ->and
            ->equalTo('OCU.Eliminado', 0);

        if ($dni != null) {
            $select->where
                ->equalTo('OS.Eliminado', 0)
                ->and
                ->equalTo('CLI.NumeroDocumento', $dni);
        }

        if ($subgrupo == 1) {
            $select->where
                ->equalTo('OSG.Eliminado', 0)
                ->and
                ->equalTo('SG.Eliminado', 0)
                ->and
                ->equalTo('ESGC.Eliminado', 0);
        }

        $select->group('O.id');
        //$select->order(array('O.Premium DESC', 'O.FechaCreacion DESC'));

        //echo str_replace('"','', $select->getSqlString()); exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getOfertaDetalle($idOferta)
    {
        $select = new Select();
        $select->from(array('OEC' => 'BNF_OfertaEmpresaCliente'));
        $select->columns(
            array(
                'LogoEmpresa' => new Expression('EP.Logo'),
                'BNF_Empresa_id',
                'nombreEmpresa' => new Expression('EP.NombreComercial'),
                'idCampania' => new Expression(
                    '(SELECT
                        CN.id
                    FROM
                        BNF_Campanias AS CN
                            INNER JOIN
                        BNF_CampaniaUbigeo AS CNU ON CN.id = CNU.BNF_Campanias_id
                            INNER JOIN
                        BNF_OfertaCampaniaUbigeo AS OCNU ON CNU.id = OCNU.BNF_CampaniaUbigeo_id
                    WHERE
                        OCNU.BNF_Oferta_id = O.id
                            AND OCNU.Eliminado = 0
                            AND CNU.Eliminado = 0
                            AND CN.Eliminado = 0
                    LIMIT 1)'
                )
            )
        );
        $select->join(
            array('O' => 'BNF_Oferta'),
            'O.id = OEC.BNF_Oferta_id',
            array(
                'idOferta' => 'id',
                'TituloCortoOferta' => 'TituloCorto',
                'TituloOferta' => 'Titulo',
                'condicionesUso' => 'CondicionesUso',
                'vigencia' => 'FechaFinVigencia',
                'datoBeneficio' => 'DatoBeneficio',
                'idTipoBeneficio' => 'BNF_TipoBeneficio_id',
                'SlugOferta' => 'Slug',
                'TipoOferta' => 'BNF_BolsaTotal_TipoPaquete_id',
                'DireccionOferta' => 'Direccion',
                'TelefonoOferta' => 'Telefono',
                'CondicionesTebca' => 'CondicionesTebca',
                'TipoEspecial' => 'TipoEspecial'
            )
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = O.BNF_BolsaTotal_Empresa_id',
            array(
                'SlugEmpresa' => 'Slug',
                'DescripcionEmpresa' => 'Descripcion',
                'DireccionEmpresa' => 'DireccionEmpresa',
                'webEmpresa' => 'SitioWeb',
                'TelefonoEmpresa' => 'Telefono',
                'DiasAtencionContacto' => 'HoraAtencionContacto',
                'HoraInicioContacto' => 'HoraAtencionInicioContacto',
                'HoraFinContacto' => 'HoraAtencionFinContacto',
                'emailEmpresa' => 'CorreoPersonaAtencion',
                'nombreEmpresa' => 'NombreComercial',
                'NombreContacto' => 'NombreContacto',
                'TelefonoContacto' => 'TelefonoContacto',
                'CorreoContacto' => 'CorreoContacto',
            )
        );

        $select->where->equalTo('O.id', $idOferta);
        $select->group('O.id');

        //echo str_replace('"','', $select->getSqlString()); exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getOfertaEmpresaCliente($idOferta, $idEmpresa)
    {
        $idOferta = (int)$idOferta;
        $idEmpresa = (int)$idEmpresa;
        $select = new Select();
        $select->from('BNF_OfertaEmpresaCliente');
        $select->columns(array('id'));
        $select->where('BNF_Oferta_id = ' . $idOferta . ' AND BNF_Empresa_id = ' . $idEmpresa);
        //echo str_replace('"','', $select->getSqlString()); exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function busquedaOferta(
        $name,
        $ubigeo,
        $empresa,
        $segmento,
        $subgrupo = 0,
        $offset = 0,
        $segmentos,
        $puntos,
        $premios,
        $segmentos_premios
    )
    {
        #region Ofertas Fase 1
        $query_1 = "SELECT O.id AS idOferta, O.Titulo AS Titulo, 1 as TipoOferta
                    FROM BNF_OfertaEmpresaCliente AS OEC 
                    INNER JOIN BNF_Oferta AS O ON O.id = OEC.BNF_Oferta_id 
                    LEFT JOIN BNF_Oferta_Atributos AS OA ON O.id = OA.BNF_Oferta_id 
                    INNER JOIN BNF_Empresa AS EP ON EP.id = O.BNF_BolsaTotal_Empresa_id 
                    INNER JOIN BNF_OfertaUbigeo AS OU ON OU.BNF_Oferta_id = O.id 
                    INNER JOIN BNF_Busqueda AS B ON B.BNF_Oferta_id = O.id ";

        if ($segmento > 0) {
            $query_1 .= "INNER JOIN BNF_OfertaSegmento AS OS ON OS.BNF_Oferta_id = O.id ";
        }

        if ($subgrupo > 0) {
            $query_1 .= "INNER JOIN BNF_OfertaSubgrupo AS OSG ON OSG.BNF_Oferta_id = O.id ";
        }

        $query_1 .= "WHERE O.Estado = 'Publicado' 
                    AND IFNULL(O.Stock, (SELECT SUM(Stock) From BNF_Oferta_Atributos WHERE BNF_Oferta_id = O.id)) > 0
                    AND IFNULL( Timestampdiff(day,CURDATE(), O.FechaFinPublicacion) ,1) >= 0 
                    AND Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0 
                    AND OEC.Eliminado = '0' 
                    AND OEC.BNF_Empresa_id = $empresa
                    AND EP.Proveedor = 1
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 1
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";

        if ($segmento > 0) {
            $query_1 .= " AND OS.Eliminado = '0'  AND OS.BNF_Segmento_id = $segmento ";
        }

        if ($subgrupo > 0) {
            $query_1 .= " AND OSG.Eliminado = '0' AND OSG.BNF_Subgrupo_id = $subgrupo ";
        }

        $query_1 .= " GROUP BY idOferta ";
        #endregion

        #region Ofertas Fase 2
        $query_2 = "SELECT OP.id AS idOferta, OP.Titulo AS Titulo, 2 as TipoOferta
                    FROM BNF2_Oferta_Puntos AS OP 
                    INNER JOIN BNF2_Oferta_Puntos_Segmentos ON OP.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Segmentos ON BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id 
                    INNER JOIN BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id 
                    INNER JOIN BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF2_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF2_Oferta_Puntos_Atributos ON OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Imagen AS IMG ON IMG.BNF2_Oferta_Puntos_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF2_Oferta_Puntos_Ubigeo AS OU ON OP.id = OU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Categoria AS OCU ON OP.id = OCU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id 
                    INNER JOIN BNF_Busqueda AS B ON OP.id = B.BNF_Oferta_id 
                    WHERE BNF2_Oferta_Puntos_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' AND EC.id = $empresa
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF2_Oferta_Puntos_Atributos WHERE BNF2_Oferta_Puntos_id = OP.id)) > 0
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaFin), 1) >= 1
                    AND EC.Cliente = 1
                    AND EP.Proveedor = 1
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND CU.BNF_Categoria_id = 9 
                    AND B.TipoOferta = 2
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE)";

        if (!empty($segmentos)) {
            $query_2 .= " AND BNF2_Segmentos.id IN (";
            $count = 0;
            foreach ($segmentos as $value) {
                if ($count == 0)
                    $query_2 .= $value;
                else
                    $query_2 .= ',' . $value;
                $count++;
            }
            $query_2 .= ") ";
        }

        $query_2 .= " GROUP BY idOferta ";
        #endregion

        #region Ofertas Fase 3
        $query_3 = "SELECT OP.id AS idOferta, OP.Titulo AS Titulo, 3 as TipoOferta
                    FROM BNF3_Oferta_Premios AS OP 
                    INNER JOIN BNF3_Oferta_Premios_Segmentos ON OP.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Segmentos ON BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id 
                    INNER JOIN BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id 
                    INNER JOIN BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF3_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF3_Oferta_Premios_Atributos ON OP.id = BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Imagen AS IMG ON IMG.BNF3_Oferta_Premios_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF3_Oferta_Premios_Ubigeo AS OU ON OP.id = OU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Categoria AS OCU ON OP.id = OCU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id  
                    INNER JOIN BNF_Busqueda AS B ON OP.id = B.BNF_Oferta_id 
                    WHERE BNF3_Oferta_Premios_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' AND EC.id = $empresa
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF3_Oferta_Premios_Atributos WHERE BNF3_Oferta_Premios_id = OP.id)) > 0
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF3_Campanias.VigenciaFin), 1) >= 1
                    AND EC.Cliente = 1
                    AND EP.Proveedor = 1
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND CU.BNF_Categoria_id = 10 
                    AND B.TipoOferta = 3
                    AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE)";

        if (!empty($segmentos_premios)) {
            $query_3 .= " AND BNF3_Segmentos.id IN (";
            $count = 0;
            foreach ($segmentos_premios as $value) {
                if ($count == 0)
                    $query_3 .= $value;
                else
                    $query_3 .= ',' . $value;
                $count++;
            }
            $query_3 .= ") ";
        }

        $query_3 .= " GROUP BY idOferta ";
        #endregion

        #region Ofertas Por Empresa

        /*
         *  la siguiente consulta solo obtiene los ids de las empresas que contienen en su nombre comercial
         *  la palabra a buscar.
         *  para luego filtrar las consultas 4, 5 y 6 por los ids del resultado
        */

        $query_emp = " SELECT DISTINCT E.id AS idOferta, E.NombreComercial AS Titulo, 0 AS TipoOferta
                    FROM BNF_Empresa AS E 
                    INNER JOIN BNF_Busqueda AS B ON E.id = B.BNF_Oferta_id 
                    WHERE E.Proveedor = 1 AND B.Empresa = 1  
                      AND MATCH(B.Descripcion) AGAINST ('$name' IN BOOLEAN MODE) ";
        $dbAdapter = $this->tableGateway->adapter->getDriver();
        $statement = $dbAdapter->createStatement($query_emp);
        $resultSet = $statement->execute();
        $resultSet->buffer();

        $empresa_p = array();
        foreach ($resultSet as $value) {
            $value = (object)$value;
            $empresa_p[] = $value->idOferta;
        }

        $query_4 = null;
        $query_5 = null;
        $query_6 = null;
        if (count($empresa_p) > 0) {

            #region Ofertas Fase 1
            $query_4 = "SELECT O.id AS idOferta, O.Titulo AS Titulo, 1 as TipoOferta
                    FROM BNF_OfertaEmpresaCliente AS OEC 
                    INNER JOIN BNF_Oferta AS O ON O.id = OEC.BNF_Oferta_id 
                    LEFT JOIN BNF_Oferta_Atributos ON O.id = BNF_Oferta_Atributos.BNF_Oferta_id 
                    INNER JOIN BNF_Empresa AS EP ON EP.id = O.BNF_BolsaTotal_Empresa_id 
                    INNER JOIN BNF_OfertaUbigeo AS OU ON OU.BNF_Oferta_id = O.id 
                    INNER JOIN BNF_Busqueda AS B ON B.BNF_Oferta_id = O.id ";

            if ($segmento > 0) {
                $query_4 .= "INNER JOIN BNF_OfertaSegmento AS OS ON OS.BNF_Oferta_id = O.id ";
            }

            if ($subgrupo > 0) {
                $query_4 .= "INNER JOIN BNF_OfertaSubgrupo AS OSG ON OSG.BNF_Oferta_id = O.id ";
            }

            $query_4 .= "WHERE O.Estado = 'Publicado' 
                    AND IFNULL(O.Stock, (SELECT SUM(Stock) From BNF_Oferta_Atributos WHERE BNF_Oferta_id = O.id)) > 0 
                    AND IFNULL( Timestampdiff(day,CURDATE(),O.FechaFinPublicacion) ,1) >= 0
                    AND Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0 
                    AND OEC.Eliminado = '0' 
                    AND OEC.BNF_Empresa_id = $empresa
                    AND EP.Proveedor = 1
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND B.TipoOferta = 1";

            $query_4 .= " AND EP.id IN (";
            $count = 0;
            foreach ($empresa_p as $value) {
                if ($count == 0)
                    $query_4 .= $value;
                else
                    $query_4 .= ',' . $value;
                $count++;
            }
            $query_4 .= ") ";

            if ($segmento > 0) {
                $query_4 .= " AND OS.Eliminado = '0'  AND OS.BNF_Segmento_id = $segmento ";
            }

            if ($subgrupo > 0) {
                $query_4 .= " AND OSG.Eliminado = '0' AND OSG.BNF_Subgrupo_id = $subgrupo ";
            }

            $query_4 .= " GROUP BY idOferta ";
            #endregion

            #region Ofertas Fase 2
            $query_5 = "SELECT OP.id AS idOferta, OP.Titulo AS Titulo, 2 as TipoOferta
                    FROM BNF2_Oferta_Puntos AS OP 
                    INNER JOIN BNF2_Oferta_Puntos_Segmentos ON OP.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Segmentos ON BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id 
                    INNER JOIN BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id 
                    INNER JOIN BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF2_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF2_Oferta_Puntos_Atributos ON OP.id = BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Imagen AS IMG ON IMG.BNF2_Oferta_Puntos_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF2_Oferta_Puntos_Ubigeo AS OU ON OP.id = OU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF2_Oferta_Puntos_Categoria AS OCU ON OP.id = OCU.BNF2_Oferta_Puntos_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id  
                    INNER JOIN BNF_Busqueda AS B ON OP.id = B.BNF_Oferta_id 
                    WHERE BNF2_Oferta_Puntos_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' AND EC.id = $empresa
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF2_Oferta_Puntos_Atributos WHERE BNF2_Oferta_Puntos_id = OP.id)) > 0 
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF2_Campanias.VigenciaFin), 1) >= 1 
                    AND EC.Cliente = 1
                    AND EP.Proveedor = 1
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND CU.BNF_Categoria_id = 9 
                    AND B.TipoOferta = 2";


                $query_5 .= " AND EP.id IN (";
                $count = 0;
                foreach ($empresa_p as $value) {
                    if ($count == 0)
                        $query_5 .= $value;
                    else
                        $query_5 .= ',' . $value;
                    $count++;
                }
                $query_5 .= ") ";

            if (!empty($segmentos)) {
                $query_5 .= " AND BNF2_Segmentos.id IN (";
                $count = 0;
                foreach ($segmentos as $value) {
                    if ($count == 0)
                        $query_5 .= $value;
                    else
                        $query_5 .= ',' . $value;
                    $count++;
                }
                $query_5 .= ") ";
            }

            $query_5 .= " GROUP BY idOferta ";
            #endregion

            #region Ofertas Fase 3
            $query_6 = "SELECT OP.id AS idOferta, OP.Titulo AS Titulo, 3 as TipoOferta
                    FROM BNF3_Oferta_Premios AS OP 
                    INNER JOIN BNF3_Oferta_Premios_Segmentos ON OP.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Segmentos ON BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id 
                    INNER JOIN BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id 
                    INNER JOIN BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id 
                    INNER JOIN BNF_Empresa AS EC ON BNF3_Campanias_Empresas.BNF_Empresa_id = EC.id 
                    LEFT JOIN BNF3_Oferta_Premios_Atributos ON OP.id = BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Imagen AS IMG ON IMG.BNF3_Oferta_Premios_id = OP.id 
                    INNER JOIN BNF_Empresa AS EP ON OP.BNF_Empresa_id = EP.id 
                    INNER JOIN BNF3_Oferta_Premios_Ubigeo AS OU ON OP.id = OU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF3_Oferta_Premios_Categoria AS OCU ON OP.id = OCU.BNF3_Oferta_Premios_id 
                    INNER JOIN BNF_CategoriaUbigeo AS CU ON CU.id = OCU.BNF_CategoriaUbigeo_id  
                    INNER JOIN BNF_Busqueda AS B ON OP.id = B.BNF_Oferta_id 
                    WHERE BNF3_Oferta_Premios_Segmentos.Eliminado = '0' 
                    AND OP.Estado = 'Publicado' AND EC.id = $empresa
                    AND IFNULL(OP.Stock, (SELECT SUM(Stock) From BNF3_Oferta_Premios_Atributos WHERE BNF3_Oferta_Premios_id = OP.id)) > 0
                    AND IFNULL(TIMESTAMPDIFF(DAY, CURDATE(), BNF3_Campanias.VigenciaFin), 1) >= 1 
                    AND EC.Cliente = 1
                    AND EP.Proveedor = 1
                    AND OU.Eliminado = '0' 
                    AND OU.BNF_Ubigeo_id = $ubigeo 
                    AND CU.BNF_Categoria_id = 10 
                    AND B.TipoOferta = 3";


            $query_6 .= " AND EP.id IN (";
            $count = 0;
            foreach ($empresa_p as $value) {
                if ($count == 0)
                    $query_6 .= $value;
                else
                    $query_6 .= ',' . $value;
                $count++;
            }
            $query_6 .= ") ";

            if (!empty($segmentos_premios)) {
                $query_6 .= " AND BNF3_Segmentos.id IN (";
                $count = 0;
                foreach ($segmentos_premios as $value) {
                    if ($count == 0)
                        $query_6 .= $value;
                    else
                        $query_6 .= ',' . $value;
                    $count++;
                }
                $query_6 .= ") ";
            }

            $query_6 .= " GROUP BY idOferta ";
            #endregion
        }
        #endregion

        if (empty($puntos) && empty($premios)) {
            $query = $query_1 . (($empresa_p != null) ? " UNION " . $query_4 : '');
        } elseif (!empty($puntos) && empty($premios)) {
            $query = $query_1 . " UNION " . $query_2 .
                (($empresa_p != null) ? " UNION " . $query_4 . " UNION " . $query_5 : '');
        }elseif (empty($puntos) && !empty($premios)) {
            $query = $query_1 . " UNION " . $query_3 .
                (($empresa_p != null) ? " UNION " . $query_4 . " UNION " . $query_6 : '');
        } else {
            $query = $query_1 . " UNION " . $query_2 . " UNION " . $query_3 .
                (($empresa_p != null) ? " UNION " . $query_4 . " UNION " . $query_5 . " UNION " . $query_6 : '');
        }

        $query .= " ORDER BY idOferta DESC LIMIT 9 OFFSET $offset";

        //echo str_replace('"','', $query); exit;

        $dbAdapter = $this->tableGateway->adapter->getDriver();
        $statement = $dbAdapter->createStatement($query);
        $resultSet = $statement->execute();
        $resultSet->buffer();
        return $resultSet;
    }

    public function totalOfertasEP($empresa)
    {
        $select = new Select();
        $select->from(array('OEC' => 'BNF_OfertaEmpresaCliente'));
        $select->columns(
            array(
                'totalOfertasEP' => new Expression('count(OEC.BNF_Oferta_id)'),
            )
        );

        $select->join(
            array('O' => 'BNF_Oferta'),
            'O.id = OEC.BNF_Oferta_id',
            array()
        );

        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = O.BNF_BolsaTotal_Empresa_id',
            array('SlugEmpresa' => 'Slug',)
        );

        $select->join(
            array('OA' => 'BNF_Oferta_Atributos'),
            'OA.BNF_Oferta_id = O.id',
            array(),
            'left'
        );

        $select->where->literal('IFNULL(OA.Stock, O.Stock) > 0')
            ->AND->equalTo('O.Estado', 'Publicado')
            ->AND->literal('IFNULL( Timestampdiff(day,CURDATE(),O.FechaFinPublicacion) ,1) >= 0')
            ->AND->literal('Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0')
            ->AND->equalTo('OEC.Eliminado', 0)
            ->AND->equalTo('OEC.BNF_Empresa_id', $empresa);

        $select->group('BNF_BolsaTotal_Empresa_id');

        //echo str_replace('"','', $select->getSqlString()); exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->toArray();
    }

    public function getCuponOfertaSlug($slug)
    {
        $select = new Select();
        $select->from(array('OEC' => 'BNF_OfertaEmpresaCliente'));
        $select->columns(
            array(
                'LogoEmpresa' => new Expression('EP.Logo'),
                'nombreEmpresa' => new Expression('EP.NombreComercial'),
                'caducadoTiempo' => new Expression(
                    'IFNULL(Timestampdiff(day,CURDATE(), O.FechaFinPublicacion),1)'
                ),
            )
        );

        $select->join(
            array('O' => 'BNF_Oferta'),
            'O.id = OEC.BNF_Oferta_id',
            array(
                'idOferta' => 'id',
                'TituloOferta' => 'Titulo',
                'condicionesUso' => 'CondicionesUso',
                'vigencia' => 'FechaFinVigencia',
                'datoBeneficio' => 'DatoBeneficio',
                'idTipoBeneficio' => 'BNF_TipoBeneficio_id',
                'SlugOferta' => 'Slug',
                'TipoOferta' => 'BNF_BolsaTotal_TipoPaquete_id',
                'DireccionOferta' => 'Direccion',
                'TelefonoOferta' => 'Telefono',
                'EstadoOferta' => 'Estado',
                'CondicionesTebca' => 'CondicionesTebca',
                'TipoAtributo' => 'TipoAtributo',
                'Stock' => new Expression(
                    'IFNULL(O.Stock, (SELECT SUM(Stock) FROM BNF_Oferta_Atributos WHERE BNF_Oferta_id = O.id))')
            ),
            'right'
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = O.BNF_BolsaTotal_Empresa_id',
            array(
                'SlugEmpresa' => 'Slug',
                'DescripcionEmpresa' => 'Descripcion',
                'webEmpresa' => 'SitioWeb',
                'DireccionEmpresa' => 'DireccionEmpresa',
                'TelefonoEmpresa' => 'Telefono'
            )
        );
        // ubigeo oferta
        $select->join(
            array('OU' => 'BNF_OfertaUbigeo'),
            'OU.BNF_Oferta_id = O.id',
            array()
        );
        $select->join(
            array('U' => 'BNF_Ubigeo'),
            'U.id = OU.BNF_Ubigeo_id',
            array()
        );
        $select->join(
            array('OCU' => 'BNF_OfertaCategoriaUbigeo'),
            'OCU.BNF_Oferta_id = O.id',
            array()
        );
        $select->join(
            array('CU' => 'BNF_CategoriaUbigeo'),
            'CU.id = OCU.BNF_CategoriaUbigeo_id AND CU.BNF_Pais_id = U.BNF_Pais_id',
            array(
                'idCategoria' => 'BNF_Categoria_id'
            )
        );
        // Campaña ubigeo
        $select->join(
            array('OCNU' => 'BNF_OfertaCampaniaUbigeo'),
            'OCNU.BNF_Oferta_id = O.id',
            array(),
            'left'
        );
        $select->join(
            array('CNU' => 'BNF_CampaniaUbigeo'),
            'CNU.BNF_Campanias_id = OCNU.BNF_CampaniaUbigeo_id AND CNU.BNF_Pais_id = U.BNF_Pais_id',
            array(),
            'left'
        );

        $select->join(
            array('OA' => 'BNF_Oferta_Atributos'),
            'O.id = OA.BNF_Oferta_id',
            array(),
            'left'
        );

        $select->where
            ->equalTo('EP.Proveedor', 1)
            ->and
            ->equalTo('OU.Eliminado', 0)
            ->and
            ->equalTo('O.Slug', $slug)
            ->and
            ->equalTo('OCU.Eliminado', 0);

        $select->group('O.id');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}
