<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:29 PM
 */

namespace Premios\Model\Table;

use Premios\Model\SegmentosPremios;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class SegmentosPremiosTable
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

    public function getAllSegmentos($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF3_Campania_id" => $id));
        return $resultSet;
    }

    public function getAllSegmentosCampania($id)
    {
        $resultSet = $this->tableGateway->select(array("BNF3_Campania_id" => $id));
        return $resultSet;
    }

    public function getSegmentosPremios($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getByName($id, $nombre)
    {
        $rowSet = $this->tableGateway->select(array('BNF3_Campania_id' => $id, 'NombreSegmento' => $nombre));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getSegmentoPersonalizadoByCampaniaId($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF3_Campania_id' => $id, "NombreSegmento" => 'Personalizada'));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getIfExistClasico($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF3_Segmentos');
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->where->equalTo('BNF3_Segmentos.id', $id)
            ->and->equalTo('TipoSegmento', 'Clasico');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select)->current();
        return !$resultSet ? false : $resultSet;
    }

    public function getIfExistPersonalizado($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF3_Segmentos');
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->where->equalTo('BNF3_Segmentos.id', $id)
            ->and->equalTo('TipoSegmento', 'Personalizado');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select)->current();
        return !$resultSet ? false : $resultSet;
    }

    public function getDetalleSegmentoInCampania($id)
    {
        $select = new Select();
        $select->from('BNF3_Segmentos');
        $select->columns(
            array(
                'id',
                'NombreSegmento',
                'CantidadPremios',
                'CantidadPersonas',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "(SELECT SUM(CantidadPremios) FROM BNF3_Asignacion_Premios
                    WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios.Eliminado = 0)"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT SUM(CantidadPremiosEliminados) FROM 
                        (SELECT DISTINCT(BNF_Cliente_id), CantidadPremiosEliminados, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE
                                EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) 
                        AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'AplicadoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(CantidadPremiosUsados), 0) FROM BNF3_Asignacion_Premios
                        WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios.Eliminado = 0)"
                ),
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPremiosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosUsados, BNF3_Segmento_id, EstadoPremios
                        FROM BNF3_Asignacion_Premios_Estado_Log
                        WHERE EstadoPremios = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE
                        BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'RedimidoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(PremiosUtilizados), 0) FROM BNF3_Cupon_Premios
                        INNER JOIN
                            BNF3_Asignacion_Premios ON BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = BNF3_Asignacion_Premios.id
                        WHERE
                            BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                                AND BNF3_Asignacion_Premios.Eliminado = 0
                                AND BNF3_Cupon_Premios.FechaRedimido IS NOT NULL)"
                ),
                'RedimidoInactivo' => new Expression(
                    "(SELECT IFNULL(SUM(PremiosUtilizados), 0) FROM BNF3_Cupon_Premios
                        INNER JOIN
                            BNF3_Asignacion_Premios ON BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = BNF3_Asignacion_Premios.id
                        WHERE
                            BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                                AND BNF3_Asignacion_Premios.Eliminado = 1
                                AND BNF3_Cupon_Premios.FechaRedimido IS NOT NULL)"
                ),
                'UsuariosAsignados' => new Expression(
                    "(SELECT 
                        IFNULL(COUNT(*), 0)
                    FROM
                        BNF3_Asignacion_Premios
                    WHERE
                        BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios.Eliminado = 0)"
                ),
                'Eliminado',
            )
        );
        $select->where->equalTo("BNF3_Campania_id", $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDetalleSegmentoAsignacion($id)
    {
        $select = new Select();
        $select->from('BNF3_Segmentos');
        $select->columns(
            array(
                'id',
                'BNF3_Campania_id',
                'NombreSegmento',
                'CantidadPremios',
                'CantidadPersonas',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "(SELECT SUM(CantidadPremios) FROM BNF3_Asignacion_Premios
                    WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios.Eliminado = 0)"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT SUM(CantidadPremiosEliminados) FROM 
                        (SELECT DISTINCT(BNF_Cliente_id), CantidadPremiosEliminados, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE
                                EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) 
                        AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'AplicadoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(CantidadPremiosUsados), 0) FROM BNF3_Asignacion_Premios
                        WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios.Eliminado = 0)"
                ),
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPremiosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosUsados, BNF3_Segmento_id, EstadoPremios
                        FROM BNF3_Asignacion_Premios_Estado_Log
                        WHERE EstadoPremios = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE
                        BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'RedimidoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(PremiosUtilizados), 0) FROM BNF3_Cupon_Premios
                        INNER JOIN
                            BNF3_Asignacion_Premios ON BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = BNF3_Asignacion_Premios.id
                        WHERE
                            BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                                AND BNF3_Asignacion_Premios.Eliminado = 0
                                AND BNF3_Cupon_Premios.FechaRedimido IS NOT NULL)"
                ),
                'RedimidoInactivo' => new Expression(
                    "(SELECT IFNULL(SUM(PremiosUtilizados), 0) FROM BNF3_Cupon_Premios
                        INNER JOIN
                            BNF3_Asignacion_Premios ON BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = BNF3_Asignacion_Premios.id
                        WHERE
                            BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                                AND BNF3_Asignacion_Premios.Eliminado = 1
                                AND BNF3_Cupon_Premios.FechaRedimido IS NOT NULL)"
                ),
                'UsuariosAsignados' => new Expression(
                    "(SELECT 
                        IFNULL(COUNT(*), 0)
                    FROM
                        BNF3_Asignacion_Premios
                    WHERE
                        BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios.Eliminado = 0)"
                ),
                'Comentario',
                'Eliminado'
            )
        );

        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );

        $select->where->equalTo("BNF3_Segmentos.id", $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getListaDetalleAsignacion($order_by, $order, $empresa = "", $campania = null)
    {
        $select = new Select();
        $select->from('BNF3_Segmentos');
        $select->columns(
            array(
                'id',
                'NombreSegmento',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "IF(BNF3_Segmentos.Eliminado = 0,IFNULL((SELECT SUM(CantidadPremios) FROM BNF3_Asignacion_Premios
                    WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios.Eliminado = 0), 0), -1)"
                ),
                'DisponibleAsignar' => new Expression(
                    "IF(BNF3_Segmentos.Eliminado = 0,(Subtotal - 
                    IFNULL((SELECT SUM(CantidadPremios) FROM BNF3_Asignacion_Premios
                        WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios.Eliminado = 0), 0) - 
                    IFNULL((SELECT SUM(CantidadPremios) FROM 
                        (SELECT DISTINCT(BNF_Cliente_id), CantidadPremios, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE  EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) 
                        AS BNF3_Asignacion_Premios_Estado_Log
                        WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado'), 0) - 
                    IFNULL((SELECT SUM(CantidadPremiosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosUsados, BNF3_Segmento_id, EstadoPremios
                        FROM BNF3_Asignacion_Premios_Estado_Log
                        WHERE EstadoPremios = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                        WHERE
                        BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado'), 0)), -1)"
                ),
                'PtosEliminado' => new Expression(
                    "(SELECT  SUM(CantidadPremiosEliminados) FROM 
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosEliminados, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT SUM(CantidadPremios) FROM 
                        (SELECT DISTINCT(BNF_Cliente_id), CantidadPremios, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE  EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) 
                        AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'Eliminado',
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPremiosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosUsados, BNF3_Segmento_id, EstadoPremios
                        FROM BNF3_Asignacion_Premios_Estado_Log
                        WHERE EstadoPremios = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE
                        BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                )
            )
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array('Campania' => 'NombreCampania', 'Tipo' => 'TipoSegmento', 'EstadoCampania' => 'EstadoCampania')
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial' , 'EmpresaId' => 'id', 'EmpresaFull' => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)"))
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa_id", $empresa);
        }

        if (!empty($campania)) {
            $select->where->equalTo("BNF3_Campanias.id", $campania);
        }

        $select->where->notEqualTo("BNF3_Campanias.EstadoCampania", 'Borrador');
        $select->where->equalTo("BNF3_Campanias.Eliminado", 0);

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF3_Segmentos.FechaCreacion DESC");
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getReporteAsignacion($empresa = "", $campania = "")
    {
        $select = new Select();
        $select->from('BNF3_Segmentos');
        $select->columns(
            array(
                'id',
                'NombreSegmento',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "IF(BNF3_Segmentos.Eliminado = 0,IFNULL((SELECT SUM(CantidadPremios) FROM BNF3_Asignacion_Premios
                    WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios.Eliminado = 0), 0), -1)"
                ),
                'DisponibleAsignar' => new Expression(
                    "IF(BNF3_Segmentos.Eliminado = 0,(Subtotal - 
                    IFNULL((SELECT SUM(CantidadPremios) FROM BNF3_Asignacion_Premios
                        WHERE BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios.Eliminado = 0), 0) - 
                    IFNULL((SELECT SUM(CantidadPremios) FROM 
                        (SELECT DISTINCT(BNF_Cliente_id), CantidadPremios, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE  EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) 
                        AS BNF3_Asignacion_Premios_Estado_Log
                        WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado'), 0) - 
                    IFNULL((SELECT SUM(CantidadPremiosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosUsados, BNF3_Segmento_id, EstadoPremios
                        FROM BNF3_Asignacion_Premios_Estado_Log
                        WHERE EstadoPremios = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                        WHERE
                        BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado'), 0)), -1)"
                ),
                'PtosEliminado' => new Expression(
                    "(SELECT  SUM(CantidadPremiosEliminados) FROM 
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosEliminados, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT SUM(CantidadPremios) FROM 
                        (SELECT DISTINCT(BNF_Cliente_id), CantidadPremios, BNF3_Segmento_id, EstadoPremios
                            FROM BNF3_Asignacion_Premios_Estado_Log
                            WHERE  EstadoPremios = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) 
                        AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                        AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                ),
                'Eliminado',
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPremiosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPremiosUsados, BNF3_Segmento_id, EstadoPremios
                        FROM BNF3_Asignacion_Premios_Estado_Log
                        WHERE EstadoPremios = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                    WHERE
                        BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            AND BNF3_Asignacion_Premios_Estado_Log.EstadoPremios = 'Cancelado')"
                )
            )
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array('Campania' => 'NombreCampania', 'Tipo' => 'TipoSegmento', 'EstadoCampania' => 'EstadoCampania')
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa_id", $empresa);
        }

        if (!empty($campania)) {
            $select->where->equalTo("BNF3_Campanias.id", $campania);
        }

        $select->where->notEqualTo("BNF3_Campanias.EstadoCampania", 'Borrador');

        $select->order("BNF3_Segmentos.id DESC");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveSegmentoP(SegmentosPremios $SegmentosPremios)
    {
        $data = array(
            'BNF3_Campania_id' => $SegmentosPremios->BNF3_Campania_id,
            'NombreSegmento' => $SegmentosPremios->NombreSegmento,
            'CantidadPersonas' => $SegmentosPremios->CantidadPersonas,
            'CantidadPremios' => $SegmentosPremios->CantidadPremios,
            'Subtotal' => $SegmentosPremios->Subtotal,
            'Comentario' => $SegmentosPremios->Comentario,
            'Eliminado' => $SegmentosPremios->Eliminado,
        );

        $id = (int)$SegmentosPremios->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getSegmentosPremios($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('SegmentosPremios id does not exist');
            }
        }
        return $id;
    }

    public function deleteSegmentoP($idSegmento)
    {
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $data['Eliminado'] = 1;
        $this->tableGateway->update($data, array('id' => (int)$idSegmento));
    }

    public function disabledSegmentoP($idCampania)
    {
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $data['Eliminado'] = 1;
        $this->tableGateway->update($data, array('BNF3_Campania_id' => (int)$idCampania));
    }
}
