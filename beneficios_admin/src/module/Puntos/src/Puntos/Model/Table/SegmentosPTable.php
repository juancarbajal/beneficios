<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:29 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\SegmentosP;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class SegmentosPTable
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
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF2_Campania_id" => $id));
        return $resultSet;
    }

    public function getAllSegmentosCampania($id)
    {
        $resultSet = $this->tableGateway->select(array("BNF2_Campania_id" => $id));
        return $resultSet;
    }

    public function getSegmentosP($id)
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
        $rowSet = $this->tableGateway->select(array('BNF2_Campania_id' => $id, 'NombreSegmento' => $nombre));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getSegmentoPersonalizadoByCampaniaId($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF2_Campania_id' => $id, "NombreSegmento" => 'Personalizada'));
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
        $select->from('BNF2_Segmentos');
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->where->equalTo('BNF2_Segmentos.id', $id)
            ->and->equalTo('TipoSegmento', 'Clasico');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select)->current();
        return !$resultSet ? false : $resultSet;
    }

    public function getIfExistPersonalizado($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF2_Segmentos');
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->where->equalTo('BNF2_Segmentos.id', $id)
            ->and->equalTo('TipoSegmento', 'Personalizado');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select)->current();
        return !$resultSet ? false : $resultSet;
    }

    public function getDetalleSegmentoInCampania($id)
    {
        $select = new Select();
        $select->from('BNF2_Segmentos');
        $select->columns(
            array(
                'id',
                'NombreSegmento',
                'CantidadPuntos',
                'CantidadPersonas',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "(SELECT SUM(CantidadPuntos) FROM BNF2_Asignacion_Puntos
                    WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                        AND BNF2_Asignacion_Puntos.Eliminado = 0)"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT 
                        SUM(CantidadPuntosEliminados)
                    FROM
                        (SELECT 
                            SUM(CantidadPuntosEliminados) AS CantidadPuntosEliminados,
                                BNF2_Segmento_id,
                                EstadoPuntos
                        FROM
                            BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                            EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'AplicadoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(CantidadPuntosUsados), 0) FROM BNF2_Asignacion_Puntos
                        WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos.Eliminado = 0)"
                ),
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPuntosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPuntosUsados, BNF2_Segmento_id, EstadoPuntos
                        FROM BNF2_Asignacion_Puntos_Estado_Log
                        WHERE EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'RedimidoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados), 0) FROM BNF2_Cupon_Puntos
                        INNER JOIN
                            BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                        INNER JOIN
                            BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                        WHERE
                            BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                                AND BNF2_Asignacion_Puntos.Eliminado = 0
                                AND BNF2_Cupon_Puntos.FechaRedimido IS NOT NULL)"
                ),
                'RedimidoInactivo' => new Expression(
                    "(SELECT IFNULL(SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados), 0) FROM BNF2_Cupon_Puntos
                        INNER JOIN
                            BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                        INNER JOIN
                            BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                        WHERE
                            BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                                AND BNF2_Asignacion_Puntos.Eliminado = 1
                                AND BNF2_Cupon_Puntos.FechaRedimido IS NOT NULL)"
                ),
                'UsuariosAsignados' => new Expression(
                    "(SELECT 
                        IFNULL(COUNT(*), 0)
                    FROM
                        BNF2_Asignacion_Puntos
                    WHERE
                        BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos.Eliminado = 0)"
                ),
                'Eliminado',
            )
        );
        $select->where->equalTo("BNF2_Campania_id", $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDetalleSegmentoAsignacion($id)
    {
        $select = new Select();
        $select->from('BNF2_Segmentos');
        $select->columns(
            array(
                'id',
                'BNF2_Campania_id',
                'NombreSegmento',
                'CantidadPuntos',
                'CantidadPersonas',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "(SELECT SUM(CantidadPuntos) FROM BNF2_Asignacion_Puntos
                    WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                        AND BNF2_Asignacion_Puntos.Eliminado = 0)"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT 
                        SUM(CantidadPuntosEliminados)
                    FROM
                        (SELECT 
                            SUM(CantidadPuntosEliminados) AS CantidadPuntosEliminados,
                                BNF2_Segmento_id,
                                EstadoPuntos
                        FROM
                            BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                            EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'AplicadoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(CantidadPuntosUsados), 0) FROM BNF2_Asignacion_Puntos
                        WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos.Eliminado = 0)"
                ),
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPuntosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPuntosUsados, BNF2_Segmento_id, EstadoPuntos
                        FROM BNF2_Asignacion_Puntos_Estado_Log
                        WHERE EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'RedimidoActivo' => new Expression(
                    "(SELECT IFNULL(SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados), 0) FROM BNF2_Cupon_Puntos
                        INNER JOIN
                            BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                        INNER JOIN
                            BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                        WHERE
                            BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                                AND BNF2_Asignacion_Puntos.Eliminado = 0
                                AND BNF2_Cupon_Puntos.FechaRedimido IS NOT NULL)"
                ),
                'RedimidoInactivo' => new Expression(
                    "(SELECT IFNULL(SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados), 0) FROM BNF2_Cupon_Puntos
                        INNER JOIN
                            BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                        INNER JOIN
                            BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                        WHERE
                            BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                                AND BNF2_Asignacion_Puntos.Eliminado = 1
                                AND BNF2_Cupon_Puntos.FechaRedimido IS NOT NULL)"
                ),
                'UsuariosAsignados' => new Expression(
                    "(SELECT 
                        IFNULL(COUNT(*), 0)
                    FROM
                        BNF2_Asignacion_Puntos
                    WHERE
                        BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos.Eliminado = 0)"
                ),
                'Comentario',
                'Eliminado'
            )
        );

        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );

        $select->where->equalTo("BNF2_Segmentos.id", $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getListaDetalleAsignacion($order_by, $order, $empresa = "", $campania = null)
    {
        $select = new Select();
        $select->from('BNF2_Segmentos');
        $select->columns(
            array(
                'id',
                'NombreSegmento',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "IF(BNF2_Segmentos.Eliminado = 0,IFNULL((SELECT SUM(CantidadPuntos) FROM BNF2_Asignacion_Puntos
                    WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                        AND BNF2_Asignacion_Puntos.Eliminado = 0), 0), -1)"
                ),
                'DisponibleAsignar' => new Expression(
                    "IF(BNF2_Segmentos.Eliminado = 0,(Subtotal - 
                    IFNULL((SELECT SUM(CantidadPuntos) FROM BNF2_Asignacion_Puntos
                        WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                        AND BNF2_Asignacion_Puntos.Eliminado = 0), 0) - 
                    IFNULL((SELECT 
                        SUM(CantidadPuntosEliminados)
                    FROM
                        (SELECT 
                            SUM(CantidadPuntosEliminados) AS CantidadPuntosEliminados,
                                BNF2_Segmento_id,
                                EstadoPuntos
                        FROM
                            BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                            EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado'), 0) - 
                    IFNULL((SELECT SUM(CantidadPuntosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPuntosUsados, BNF2_Segmento_id, EstadoPuntos
                        FROM BNF2_Asignacion_Puntos_Estado_Log
                        WHERE EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado'), 0)), -1)"
                ),
                'PtosEliminado' => new Expression(
                    "(SELECT  SUM(CantidadPuntosEliminados) FROM 
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPuntosEliminados, BNF2_Segmento_id, EstadoPuntos
                            FROM BNF2_Asignacion_Puntos_Estado_Log
                            WHERE EstadoPuntos = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT 
                        SUM(CantidadPuntosEliminados)
                    FROM
                        (SELECT 
                            SUM(CantidadPuntosEliminados) AS CantidadPuntosEliminados,
                                BNF2_Segmento_id,
                                EstadoPuntos
                        FROM
                            BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                            EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'Eliminado',
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPuntosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPuntosUsados, BNF2_Segmento_id, EstadoPuntos
                        FROM BNF2_Asignacion_Puntos_Estado_Log
                        WHERE EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                )
            )
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array('Campania' => 'NombreCampania', 'Tipo' => 'TipoSegmento', 'EstadoCampania' => 'EstadoCampania')
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa_id", $empresa);
        }

        if (!empty($campania)) {
            $select->where->equalTo("BNF2_Campanias.id", $campania);
        }

        $select->where->notEqualTo("BNF2_Campanias.EstadoCampania", 'Borrador');

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF2_Segmentos.FechaCreacion DESC");
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getReporteAsignacion($empresa = "", $campania = "")
    {
        $select = new Select();
        $select->from('BNF2_Segmentos');
        $select->columns(
            array(
                'id',
                'NombreSegmento',
                'Subtotal',
                'AsignadoActivo' => new Expression(
                    "IF(BNF2_Segmentos.Eliminado = 0,IFNULL((SELECT SUM(CantidadPuntos) FROM BNF2_Asignacion_Puntos
                    WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                        AND BNF2_Asignacion_Puntos.Eliminado = 0), 0), -1)"
                ),
                'DisponibleAsignar' => new Expression(
                    "IF(BNF2_Segmentos.Eliminado = 0,(Subtotal - 
                    IFNULL((SELECT SUM(CantidadPuntos) FROM BNF2_Asignacion_Puntos
                        WHERE BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                        AND BNF2_Asignacion_Puntos.Eliminado = 0), 0) - 
                    IFNULL((SELECT 
                        SUM(CantidadPuntosEliminados)
                    FROM
                        (SELECT 
                            SUM(CantidadPuntosEliminados) AS CantidadPuntosEliminados,
                                BNF2_Segmento_id,
                                EstadoPuntos
                        FROM
                            BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                            EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado'), 0) - 
                    IFNULL((SELECT SUM(CantidadPuntosUsados) FROM
                        (SELECT CantidadPuntosUsados, BNF2_Segmento_id, EstadoPuntos
                        FROM BNF2_Asignacion_Puntos_Estado_Log
                        WHERE EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado'), 0)), -1)"
                ),
                'PtosEliminado' => new Expression(
                    "(SELECT  SUM(CantidadPuntosEliminados) FROM 
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPuntosEliminados, BNF2_Segmento_id, EstadoPuntos
                            FROM BNF2_Asignacion_Puntos_Estado_Log
                            WHERE EstadoPuntos = 'Cancelado'
                            GROUP BY BNF_Cliente_id
                            ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'AsignadoEliminado' => new Expression(
                    "(SELECT 
                        SUM(CantidadPuntosEliminados)
                    FROM
                        (SELECT 
                            SUM(CantidadPuntosEliminados) AS CantidadPuntosEliminados,
                                BNF2_Segmento_id,
                                EstadoPuntos
                        FROM
                            BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                            EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                ),
                'Eliminado',
                'AplicadoInactivo' => new Expression(
                    "(SELECT SUM(CantidadPuntosUsados) FROM
                        (SELECT DISTINCT (BNF_Cliente_id), CantidadPuntosUsados, BNF2_Segmento_id, EstadoPuntos
                        FROM BNF2_Asignacion_Puntos_Estado_Log
                        WHERE EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                    WHERE
                        BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            AND BNF2_Asignacion_Puntos_Estado_Log.EstadoPuntos = 'Cancelado')"
                )
            )
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array('Campania' => 'NombreCampania', 'Tipo' => 'TipoSegmento', 'EstadoCampania' => 'EstadoCampania')
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa_id", $empresa);
        }

        if (!empty($campania)) {
            $select->where->equalTo("BNF2_Campanias.id", $campania);
        }

        $select->where->notEqualTo("BNF2_Campanias.EstadoCampania", 'Borrador');

        $select->order("BNF2_Segmentos.id DESC");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveSegmentoP(SegmentosP $segmentosP)
    {
        $data = array(
            'BNF2_Campania_id' => $segmentosP->BNF2_Campania_id,
            'NombreSegmento' => $segmentosP->NombreSegmento,
            'CantidadPersonas' => $segmentosP->CantidadPersonas,
            'CantidadPuntos' => $segmentosP->CantidadPuntos,
            'Subtotal' => $segmentosP->Subtotal,
            'Comentario' => $segmentosP->Comentario,
            'Eliminado' => $segmentosP->Eliminado,
        );

        $id = (int)$segmentosP->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getSegmentosP($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('SegmentosP id does not exist');
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
        $this->tableGateway->update($data, array('BNF2_Campania_id' => (int)$idCampania));
    }
}
