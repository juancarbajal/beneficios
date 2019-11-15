<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:14 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\Asignacion;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AsignacionTable
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

    public function getAsignacion($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getAsignacionValid($id, $estado)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(
            array('id' => $id, "EstadoPuntos" => $estado)
        );
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getAsignacionCliente($segmento, $cliente)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->where->equalTo("BNF2_Segmento_id", $segmento)
            ->and->equalTo('BNF_Cliente_id', $cliente);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getAsignacionByCampania($campania)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->where->equalTo('BNF2_Campanias.id', $campania)
            ->and->equalTo('BNF2_Segmentos.Eliminado', 0);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getAsignacionBySegmento($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->where->equalTo('BNF2_Segmento_id', $segmento);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresasAsignacion()
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias_Empresas.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF2_Campanias_Empresas.BNF_Empresa_id',
            array(
                'id' => 'id',
                "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)")
            )
        );
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTotalAssigned($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('TotalAsignados' => new Expression("SUM(CantidadPuntos)")));
        $select->where->equalTo("BNF2_Segmento_id", $segmento)
            ->AND->equalTo('BNF2_Asignacion_Puntos.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalAssignedByCampaign($campaign)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(
            array(
                'TotalAsignados' => new Expression(
                    "(SUM(BNF2_Asignacion_Puntos.CantidadPuntos) / PresupuestoNegociado) * 100"
                )
            )
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->where->equalTo("BNF2_Campanias.id", $campaign)
            ->AND->equalTo('BNF2_Asignacion_Puntos.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalUsers($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('TotalUsuarios' => new Expression("COUNT(BNF_Cliente_id)")));
        $select->where->equalTo("BNF2_Segmento_id", $segmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalAssignedDisabled($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('TotalAsignados' => new Expression("SUM(CantidadPuntos)")));
        $select->where->equalTo("BNF2_Segmento_id", $segmento)
            ->AND->equalTo('BNF2_Asignacion_Puntos.EstadoPuntos', 'Desactivado')
            ->AND->equalTo('BNF2_Asignacion_Puntos.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalUsersDisabled($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('TotalUsuarios' => new Expression("COUNT(BNF_Cliente_id)")));
        $select->where->equalTo("BNF2_Segmento_id", $segmento)
            ->AND->equalTo('BNF2_Asignacion_Puntos.EstadoPuntos', 'Desactivado')
            ->AND->equalTo('BNF2_Asignacion_Puntos.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getPersonalizedAssigned($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('*'));
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array('BNF_Cliente_id' => 'NumeroDocumento')
        );
        $select->where->equalTo("BNF2_Segmento_id", $segmento);
        //$select->where->equalTo("BNF2_Asignacion_Puntos.Eliminado", 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPersonalizedAssignedDisabled($segmento, $documento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('*'));
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento', 'Nombre', 'Apellido')
        );

        $select->where->equalTo("BNF2_Asignacion_Puntos.BNF2_Segmento_id", $segmento)
            ->AND->equalTo("BNF_Cliente.NumeroDocumento", $documento)
            ->AND->equalTo("BNF2_Asignacion_Puntos.EstadoPuntos", "Desactivado");
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalPuntosAplicados($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('TotalAplicados' => new Expression("SUM(CantidadPuntosUsados)")));
        $select->where->equalTo("BNF2_Segmento_id", $segmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalPuntosDisponibles($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('TotalDisponibles' => new Expression("SUM(CantidadPuntosDisponibles)")));
        $select->where->equalTo("BNF2_Segmento_id", $segmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getDetalleUsuariosDisabled($segmento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(array('CantidadPuntos'));
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento')
        );
        $select->where->equalTo("BNF2_Segmento_id", $segmento)
            ->and->equalTo("BNF2_Asignacion_Puntos.EstadoPuntos", "Desactivado");
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getListaUsuariosAsignacion($segmento, $documento = "")
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(
            array(
                'id',
                'CantidadPuntos',
                'CantidadPuntosUsados',
                'CantidadPuntosDisponibles',
                'CantidadPuntosEliminados',

                'Redimidos' => new Expression(
                    "(SELECT 
                        SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados)
                    FROM
                        BNF2_Cupon_Puntos
                            INNER JOIN
                        BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                    WHERE
                        FechaRedimido IS NOT NULL
                            AND BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id)"),
                'Eliminado',
                'EstadoPuntos',
                'FechaCreacion',
            )
        );
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento', 'Nombre', 'Apellido')
        );

        $select->where->equalTo("BNF2_Segmento_id", $segmento);
        if (!empty($documento)) {
            $select->where->like("BNF_Cliente.NumeroDocumento", '%' . $documento . '%');
        }

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getListaUsuariosCancelacion($order_by, $order, $empresa, $campania, $segmento, $nombre = "", $estado = "Activado")
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(
            array(
                'id',
                'EstadoPuntos',
                'CantidadPuntos',
                'CantidadPuntosUsados',
                'CantidadPuntosDisponibles',
                'CantidadPuntosEliminados',
                'Eliminado',
                'Redimidos' => new Expression(
                    "(SELECT 
                        SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados)
                    FROM
                        BNF2_Cupon_Puntos
                            INNER JOIN
                        BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                    WHERE
                        FechaRedimido IS NOT NULL
                            AND BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id)")
            )
        );
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento', 'Nombre', 'Apellido')
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
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

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($campania)) {
            $select->where->equalTo("BNF2_Campanias.id", $campania);
        }

        if (!empty($segmento)) {
            $select->where->equalTo("BNF2_Segmentos.id", $segmento);
        }

        $select->where->equalTo("BNF2_Asignacion_Puntos.EstadoPuntos", (!empty($estado)) ? $estado : "Activado");

        if (!empty($nombre)) {
            $select->where->NEST
                ->like('BNF_Cliente.Nombre', '%' . $nombre . '%')
                ->or
                ->like('BNF_Cliente.Apellido', '%' . $nombre . '%')
                ->or
                ->like('BNF_Cliente.NumeroDocumento', $nombre);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF2_Asignacion_Puntos.id DESC");
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function saveAsignacion(Asignacion $asignacion)
    {
        $data = array(
            'BNF2_Segmento_id' => $asignacion->BNF2_Segmento_id,
            'BNF_Cliente_id' => $asignacion->BNF_Cliente_id,
            'CantidadPuntos' => $asignacion->CantidadPuntos,
            'CantidadPuntosDisponibles' => $asignacion->CantidadPuntosDisponibles,
            'CantidadPuntosEliminados' => $asignacion->CantidadPuntosEliminados,
            'EstadoPuntos' => $asignacion->EstadoPuntos,
            'Eliminado' => $asignacion->Eliminado,
        );

        $id = (int)$asignacion->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getAsignacion($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Asignacion id does not exist');
            }
        }
        return $id;
    }

    public function cambiarEstadoPuntosAsignacion($asignacion, $estado, $puntos = 0)
    {
        if (!empty($puntos) && $estado == "Cancelado") {
            $data['CantidadPuntosEliminados'] = $puntos;
            $data['CantidadPuntosDisponibles'] = 0;
            $data['CantidadPuntos'] = 0;
            $data['Eliminado'] = 1;
        }
        $data['EstadoPuntos'] = $estado;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => $asignacion));
    }

    public function searchDocument($empresa, $campania, $documento)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
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

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->and->equalTo("BNF2_Campanias.id", $campania)
            ->and->equalTo("BNF_Cliente.NumeroDocumento", $documento)
            ->and->equalTo("BNF2_Asignacion_Puntos.Eliminado", 0);

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        if (!$resultSet->count()) {
            return false;
        }
        return true;
    }

    public function reporteComportamiento(
        $usarEmpresa = null,
        $empresa = null,
        $usarCampania = null,
        $campania = null,
        $usarSegmento = null,
        $segmento = null,
        $usarUsuario = null,
        $usuario = null
    )
    {
        if ($usarUsuario == 1) {
            $where = "AND BNF_Cliente.id = CL.id
                AND BNF_Empresa.id = EP.id
                AND BNF2_Segmentos.id = SP.id
                AND BNF2_Campanias.id = CP.id)";
        } elseif ($usarSegmento == 1) {
            $where = "AND BNF2_Segmentos.id = SP.id)";
        } elseif ($usarCampania == 1) {
            $where = "AND BNF2_Campanias.id = CP.id)";
        } else {
            $where = "AND BNF_Empresa.id = EP.id)";
        }

        #region Consultas
        $usuariosAsignados = "(SELECT 
                            COUNT(*) 
                    FROM BNF2_Asignacion_Puntos
                            INNER JOIN
                        BNF_Cliente ON BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado' " . $where;

        $puntosAsignados = "(SELECT 
                        IFNULL(SUM(BNF2_Asignacion_Puntos.CantidadPuntos), 0)
                    FROM
                        BNF2_Asignacion_Puntos
                            INNER JOIN
                        BNF_Cliente ON BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado' " . $where;

        $usuariosAplicados = "(SELECT 
                        COUNT(CantidadPuntosUsados)
                    FROM
                        BNF2_Asignacion_Puntos
                            INNER JOIN
                        BNF_Cliente ON BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado'
                            AND BNF2_Asignacion_Puntos.CantidadPuntosUsados > 0
                            AND BNF2_Segmentos.Eliminado = 0 
                            AND BNF2_Campanias.Eliminado = 0 " . $where;

        $puntosAplicados = "(SELECT 
                        IFNULL(SUM(CantidadPuntosUsados), 0)
                    FROM
                        BNF2_Asignacion_Puntos
                            INNER JOIN
                        BNF_Cliente ON BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado'
                            AND BNF2_Asignacion_Puntos.CantidadPuntosUsados > 0
                            AND BNF2_Segmentos.Eliminado = 0 
                            AND BNF2_Campanias.Eliminado = 0 " . $where;

        $usuariosRedimidos = "(SELECT 
                        COUNT(DISTINCT BNF_Cliente.id)
                    FROM
                        BNF2_Cupon_Puntos
                            INNER JOIN
                        BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                            INNER JOIN
                        BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                            INNER JOIN
                        BNF_Cliente ON BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado'                            
                            AND BNF2_Segmentos.Eliminado = 0 
                            AND BNF2_Campanias.Eliminado = 0
                            AND BNF2_Campanias_Empresas.Eliminado = 0
                            AND FechaRedimido IS NOT NULL " . $where;

        $puntosRedimidos = "(SELECT 
                        IFNULL(SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados), 0)
                    FROM
                        BNF2_Cupon_Puntos
                            INNER JOIN
                        BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                            INNER JOIN
                        BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                            INNER JOIN
                        BNF_Cliente ON BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado'
                            AND BNF2_Segmentos.Eliminado = 0 
                            AND BNF2_Campanias.Eliminado = 0
                            AND BNF2_Campanias_Empresas.Eliminado = 0
                            AND FechaRedimido IS NOT NULL " . $where;
        #endregion

        $select = new Select();
        $select->from(array('ASP' => 'BNF2_Asignacion_Puntos'));
        $select->columns(
            array(
                'UsuAsignados' => new Expression($usuariosAsignados),
                'TotalAsignados' => new Expression($puntosAsignados),
                'UsuAplicados' => new Expression($usuariosAplicados),
                'TotalAplicados' => new Expression($puntosAplicados),
                'UsuRedimidos' => new Expression($usuariosRedimidos),
                'Redimidos' => new Expression($puntosRedimidos),
                'Correos' => new Expression(
                    "(SELECT DISTINCT
                        GROUP_CONCAT(DISTINCT TRIM(BNF_ClienteCorreo.Correo)
                                SEPARATOR ', ')
                    FROM
                        BNF_ClienteCorreo
                            INNER JOIN
                        BNF2_Cupon_Puntos ON BNF_ClienteCorreo.id = BNF2_Cupon_Puntos.BNF_ClienteCorreo_id
                            INNER JOIN
                        BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                            INNER JOIN
                        BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id
                    WHERE
                        BNF_ClienteCorreo.Correo IS NOT NULL
                            AND TRIM(BNF_ClienteCorreo.Correo) != ''
                            AND BNF_ClienteCorreo.BNF_Cliente_id = CL.id
                            AND BNF2_Segmentos.id = SP.id)"
                )
            )
        );
        $select->join(
            array('CL' => 'BNF_Cliente'),
            'ASP.BNF_Cliente_id = CL.id',
            array('NumeroDocumento')
        );
        $select->join(
            array('PC' => 'BNF_Preguntas'),
            'CL.id = PC.BNF_Cliente_id',
            array()
        );
        $select->join(
            array('SP' => 'BNF2_Segmentos'),
            'ASP.BNF2_Segmento_id = SP.id',
            array('Segmento' => 'NombreSegmento')
        );
        $select->join(
            array('CP' => 'BNF2_Campanias'),
            'SP.BNF2_Campania_id = CP.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            array('CEP' => 'BNF2_Campanias_Empresas'),
            'CP.id = CEP.BNF2_Campania_id',
            array()
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'CEP.BNF_Empresa_id = EP.id',
            array("Empresa" => 'NombreComercial')
        );

        if ($usarEmpresa && !empty($empresa)) {
            $select->where->equalTo("EP.id", $empresa);
        }

        if ($usarCampania && !empty($campania)) {
            $select->where->equalTo("CP.id", $campania);
        }

        if ($usarSegmento && !empty($segmento)) {
            $select->where->equalTo("SP.id", $segmento);
        }

        if ($usarUsuario && !empty($usuario)) {
            $select->where->NEST
                ->like('PC.Pregunta01', '%' . $usuario . '%')
                ->OR
                ->like('PC.Pregunta02', '%' . $usuario . '%')
                ->OR
                ->like('CL.NumeroDocumento', '%' . $usuario . '%')
                ->UNNEST;
        }

        $select->where->equalTo("CEP.Eliminado", 0);
        $select->where->equalTo("CP.Eliminado", 0);
        $select->where->equalTo("SP.Eliminado", 0);
        $select->where->equalTo("ASP.Eliminado", 0);
        $select->where->equalTo("ASP.EstadoPuntos", 'Activado');

        if ($usarUsuario) {
            $select->group("CP.id");
            $select->group("SP.id");
            $select->group("CL.id");
            $select->order("CP.id DESC");
            $select->order("SP.id");
            $select->order("CL.id DESC");
        } elseif ($usarSegmento) {
            $select->group("SP.id");
            $select->order("CP.id DESC");
            $select->order("SP.id");
        } elseif ($usarCampania) {
            $select->group("CP.id");
            $select->order("CP.id DESC");
        } else {
            $select->group("EP.id");
            $select->order("EP.id DESC");
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function reporteDemografico(
        $usarEmpresa = null,
        $empresa = null,
        $usarCampania = null,
        $campania = null,
        $usarSegmento = null,
        $segmento = null,
        $usarUsuario = null,
        $usuario = null
    )
    {
        $select = new Select();
        $select->from(array("ASP" => 'BNF2_Asignacion_Puntos'));
        $select->columns(
            array(
                'Correos' => new Expression(
                    "(SELECT DISTINCT
                        GROUP_CONCAT(DISTINCT TRIM(BNF_ClienteCorreo.Correo)
                                SEPARATOR ', ')
                    FROM
                        BNF_ClienteCorreo
                            INNER JOIN
                        BNF2_Cupon_Puntos ON BNF_ClienteCorreo.id = BNF2_Cupon_Puntos.BNF_ClienteCorreo_id
                            INNER JOIN
                        BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                            INNER JOIN
                        BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id
                    WHERE
                        BNF_ClienteCorreo.Correo IS NOT NULL
                            AND TRIM(BNF_ClienteCorreo.Correo) != ''
                            AND BNF_ClienteCorreo.BNF_Cliente_id = CL.id
                            AND BNF2_Segmentos.id = SP.id)"
                )
            )
        );
        $select->join(
            array('CL' => 'BNF_Cliente'),
            'ASP.BNF_Cliente_id = CL.id',
            array('NumeroDocumento')
        );
        $select->join(
            array('PC' => 'BNF_Preguntas'),
            'CL.id = PC.BNF_Cliente_id',
            array(
                'Pregunta01',
                'Pregunta02',
                'Pregunta03',
                'Pregunta04',
                'Pregunta05',
                'Pregunta06',
                'Pregunta07',
                'Pregunta08' => new Expression(
                    "(CASE Pregunta08 WHEN 0 THEN 'Ninguno' ELSE Pregunta08 END)"
                ),
                'Pregunta09',
                'Pregunta10'
            )
        );
        $select->join(
            array('SP' => 'BNF2_Segmentos'),
            'ASP.BNF2_Segmento_id = SP.id',
            array('Segmento' => 'NombreSegmento')
        );
        $select->join(
            array('CP' => 'BNF2_Campanias'),
            'SP.BNF2_Campania_id = CP.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            array('CEP' => 'BNF2_Campanias_Empresas'),
            'CP.id = CEP.BNF2_Campania_id',
            array()
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'CEP.BNF_Empresa_id = EP.id',
            array("Empresa" => 'NombreComercial')
        );

        if ($usarUsuario && !empty($usuario)) {
            $select->where->NEST
                ->like('PC.Pregunta01', '%' . $usuario . '%')
                ->OR
                ->like('PC.Pregunta02', '%' . $usuario . '%')
                ->OR
                ->like('CL.NumeroDocumento', '%' . $usuario . '%')
                ->UNNEST;
        }

        if ($usarEmpresa && !empty($empresa)) {
            $select->where->equalTo("EP.id", $empresa);
        }

        if ($usarCampania && !empty($campania)) {
            $select->where->equalTo("CP.id", $campania);
        }

        if ($usarSegmento && !empty($segmento)) {
            $select->where->equalTo("SP.id", $segmento);
        }

        $select->where->equalTo("ASP.Eliminado", 0);
        $select->where->equalTo("ASP.EstadoPuntos", 'Activado');
        $select->where->equalTo("CEP.Eliminado", 0);
        $select->where->equalTo("CP.Eliminado", 0);
        $select->where->equalTo("SP.Eliminado", 0);

        $select->group("CP.id");
        $select->group("SP.id");
        $select->group("CL.id");
        $select->order("CP.id DESC");
        $select->order("SP.id");
        $select->order("CL.id DESC");
        $select->order("CEP.BNF_Empresa_id");

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function reportePreferencia(
        $usarEmpresa,
        $empresa = null,
        $usarCampania = null,
        $campania = null,
        $usarSegmento = null,
        $segmento = null,
        $usarUsuario = null,
        $usuario = null
    )
    {
        if ($usarUsuario) {
            $totalAplicados = "(SELECT 
                                    BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados
                                FROM
                                    BNF2_Cupon_Puntos
                                        INNER JOIN
                                    BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                                        INNER JOIN
                                    BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                                WHERE
                                    BNF2_Cupon_Puntos.id = COP.id
                                        AND BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = ASP.id)";
        } else {
            $totalAplicados = "
            (SELECT
                IFNULL(SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados), 0)
            FROM
                BNF2_Asignacion_Puntos
                    INNER JOIN
                BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                    INNER JOIN
                BNF2_Cupon_Puntos ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                    LEFT JOIN
                BNF_Rubro ON BNF_Rubro.id = BNF2_Cupon_Puntos.BNF_Rubro_id
                    INNER JOIN
                BNF2_Segmentos ON BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id
                    INNER JOIN
                BNF2_Campanias ON BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id
            WHERE
                BNF2_Cupon_Puntos.BNF_Cliente_id = BNF2_Asignacion_Puntos.BNF_Cliente_id
                    AND BNF2_Cupon_Puntos.BNF_Empresa_id = EP.id
                    AND BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = ASP.id
                    AND BNF2_Asignacion_Puntos.Eliminado = '0'
                    AND BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado'
                    AND BNF2_Asignacion_Puntos.CantidadPuntosUsados > 0
                    AND BNF_Rubro.id = RB.id
                    AND BNF2_Segmentos.id = SP.id
                    AND BNF2_Campanias.id = CP.id)";
        }

        if ($usarUsuario) {
            $redimidos = "(SELECT 
                                IFNULL(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados, 0)
                            FROM
                                BNF2_Cupon_Puntos
                                    INNER JOIN
                                BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                                    INNER JOIN
                                BNF2_Asignacion_Puntos ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                            WHERE
                                FechaRedimido IS NOT NULL
                                    AND BNF2_Cupon_Puntos.id = COP.id
                                    AND BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = ASP.id)";
        } else {
            $redimidos = "
            (SELECT 
                IFNULL(SUM(BNF2_Cupon_Puntos_Asignacion.PuntosUtilizados), 0)
            FROM
                BNF2_Asignacion_Puntos
                    INNER JOIN
                BNF2_Cupon_Puntos_Asignacion ON BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = BNF2_Asignacion_Puntos.id
                    INNER JOIN
                BNF2_Cupon_Puntos ON BNF2_Cupon_Puntos.id = BNF2_Cupon_Puntos_Asignacion.BNF2_Cupon_Puntos_id
                    LEFT JOIN
                BNF_Rubro ON BNF_Rubro.id = BNF2_Cupon_Puntos.BNF_Rubro_id
                    INNER JOIN
                BNF2_Segmentos ON BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id
                    INNER JOIN
                BNF2_Campanias ON BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id
            WHERE
                BNF2_Cupon_Puntos.BNF_Cliente_id = BNF2_Asignacion_Puntos.BNF_Cliente_id
                    AND FechaRedimido IS NOT NULL
                    AND BNF2_Cupon_Puntos.BNF_Empresa_id = EP.id
                    AND BNF2_Cupon_Puntos_Asignacion.BNF2_Asignacion_Puntos_id = ASP.id
                    AND BNF2_Asignacion_Puntos.Eliminado = '0'
                    AND BNF2_Asignacion_Puntos.EstadoPuntos = 'Activado'
                    AND BNF2_Asignacion_Puntos.CantidadPuntosUsados > 0
                    AND BNF_Rubro.id = RB.id
                    AND BNF2_Segmentos.id = SP.id
                    AND BNF2_Campanias.id = CP.id)";
        }

        $select = new Select();
        $select->from(array('ASP' => 'BNF2_Asignacion_Puntos'));
        $select->columns(
            array(
                'TotalAplicados' => new Expression($totalAplicados),
                'Redimidos' => new Expression($redimidos)
            )
        );
        $select->join(
            array('COP' => 'BNF2_Cupon_Puntos'),
            'COP.BNF_Cliente_id = ASP.BNF_Cliente_id',
            array(),
            "left"
        );
        $select->join(
            array('OF' => 'BNF2_Oferta_Puntos'),
            'OF.id = COP.BNF2_Oferta_Puntos_id',
            array(),
            "left"
        );
        $select->join(
            array('OFR' => 'BNF2_Oferta_Puntos_Rubro'),
            'OFR.BNF2_Oferta_Puntos_id = OF.id',
            array(),
            "left"
        );
        $select->join(
            array('RB' => 'BNF_Rubro'),
            'RB.id = COP.BNF_Rubro_id',
            array('Rubro' => 'Nombre'),
            "left"
        );
        $select->join(
            array('SP' => 'BNF2_Segmentos'),
            'SP.id = ASP.BNF2_Segmento_id',
            array('Segmento' => 'NombreSegmento')
        );
        $select->join(
            array('CP' => 'BNF2_Campanias'),
            'SP.BNF2_Campania_id = CP.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            array('CEP' => 'BNF2_Campanias_Empresas'),
            'CP.id = CEP.BNF2_Campania_id',
            array()
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'CEP.BNF_Empresa_id = EP.id',
            array("Empresa" => 'NombreComercial')
        );

        if ($usarUsuario) {
            $select->join(
                array('CL' => 'BNF_Cliente'),
                'CL.id = ASP.BNF_Cliente_id',
                array('NumeroDocumento')
            );

            $select->join(
                array('PC' => 'BNF_Preguntas'),
                'CL.id = PC.BNF_Cliente_id',
                array(
                    'Pregunta01',
                    'Pregunta02',
                    'Pregunta03',
                    'Pregunta04',
                    'Pregunta05',
                    'Pregunta06',
                    'Pregunta07',
                    'Pregunta08' => new Expression(
                        "(CASE Pregunta08 WHEN 0 THEN 'Ninguno' ELSE Pregunta08 END)"
                    ),
                    'Pregunta09',
                    'Pregunta10'
                )
            );
        }

        if ($usarEmpresa && !empty($empresa)) {
            $select->where->equalTo("EP.id", $empresa);
        }

        if ($usarCampania && !empty($campania)) {
            $select->where->equalTo("CP.id", $campania);
        }

        if ($usarSegmento && !empty($segmento)) {
            $select->where->equalTo("SP.id", $segmento);
        }

        if ($usarUsuario && !empty($usuario)) {
            $select->where->NEST
                ->like('PC.Pregunta01', '%' . $usuario . '%')
                ->OR
                ->like('PC.Pregunta02', '%' . $usuario . '%')
                ->OR
                ->like('CL.NumeroDocumento', '%' . $usuario . '%')
                ->UNNEST;
        }

        $select->where->equalTo("ASP.Eliminado", 0);
        $select->where->equalTo("ASP.EstadoPuntos", 'Activado');
        $select->where->equalTo("CP.Eliminado", 0);
        $select->where->equalTo("SP.Eliminado", 0);

        $select->quantifier('DISTINCT');

        if ($usarUsuario) {
            $select->order("CP.id DESC");
            $select->order("SP.id");
            $select->order("CL.id DESC");
            $select->order("EP.id");
            $select->order("RB.id");
        } else {
            $select->order("CP.id DESC");
            $select->order("SP.id");
            $select->order("EP.id");
            $select->order("RB.id");
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function actualizarAsignacion($data, $id)
    {
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }
}
