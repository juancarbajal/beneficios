<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:14 PM
 */

namespace Premios\Model\Table;

use Premios\Model\AsignacionPremios;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AsignacionPremiosTable
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
            array('id' => $id, "EstadoPremios" => $estado)
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
        $select->from('BNF3_Asignacion_Premios');
        $select->where->equalTo("BNF3_Segmento_id", $segmento)
            ->and->equalTo('BNF_Cliente_id', $cliente);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getAsignacionByCampania($campania)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array()
        );
        $select->where->equalTo('BNF3_Campanias.id', $campania)
            ->and->equalTo('BNF3_Segmentos.Eliminado', 0);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getAsignacionBySegmento($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->where->equalTo('BNF3_Segmento_id', $segmento);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresasAsignacion()
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias_Empresas.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF3_Campanias_Empresas.BNF_Empresa_id',
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
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('TotalAsignados' => new Expression("SUM(CantidadPremios)")));
        $select->where->equalTo("BNF3_Segmento_id", $segmento)
            ->AND->equalTo('BNF3_Asignacion_Premios.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalAssignedByCampaign($campaign)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(
            array(
                'TotalAsignados' => new Expression(
                    "(SUM(BNF3_Asignacion_Premios.CantidadPremios) / PresupuestoNegociado) * 100"
                )
            )
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->where->equalTo("BNF3_Campanias.id", $campaign)
            ->AND->equalTo('BNF3_Asignacion_Premios.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalUsers($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('TotalUsuarios' => new Expression("COUNT(BNF_Cliente_id)")));
        $select->where->equalTo("BNF3_Segmento_id", $segmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalAssignedDisabled($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('TotalAsignados' => new Expression("SUM(CantidadPremios)")));
        $select->where->equalTo("BNF3_Segmento_id", $segmento)
            ->AND->equalTo('BNF3_Asignacion_Premios.EstadoPremios', 'Desactivado')
            ->AND->equalTo('BNF3_Asignacion_Premios.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalUsersDisabled($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('TotalUsuarios' => new Expression("COUNT(BNF_Cliente_id)")));
        $select->where->equalTo("BNF3_Segmento_id", $segmento)
            ->AND->equalTo('BNF3_Asignacion_Premios.EstadoPremios', 'Desactivado')
            ->AND->equalTo('BNF3_Asignacion_Premios.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getPersonalizedAssigned($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('*'));
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array('BNF_Cliente_id' => 'NumeroDocumento')
        );
        $select->where->equalTo("BNF3_Segmento_id", $segmento);
        //$select->where->equalTo("BNF3_Asignacion_Premios.Eliminado", 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPersonalizedAssignedDisabled($segmento, $documento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('*'));
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento', 'Nombre', 'Apellido')
        );

        $select->where->equalTo("BNF3_Asignacion_Premios.BNF3_Segmento_id", $segmento)
            ->AND->equalTo("BNF_Cliente.NumeroDocumento", $documento)
            ->AND->equalTo("BNF3_Asignacion_Premios.EstadoPremios", "Desactivado");
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalPremiosAplicados($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('TotalAplicados' => new Expression("SUM(CantidadPremiosUsados)")));
        $select->where->equalTo("BNF3_Segmento_id", $segmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getTotalPremiosDisponibles($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('TotalDisponibles' => new Expression("SUM(CantidadPremiosDisponibles)")));
        $select->where->equalTo("BNF3_Segmento_id", $segmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getDetalleUsuariosDisabled($segmento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(array('CantidadPremios'));
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento')
        );
        $select->where->equalTo("BNF3_Segmento_id", $segmento)
            ->and->equalTo("BNF3_Asignacion_Premios.EstadoPremios", "Desactivado");
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getListaUsuariosAsignacion($segmento, $documento = "")
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(
            array(
                'id',
                'CantidadPremios',
                'CantidadPremiosUsados',
                'CantidadPremiosDisponibles',
                'CantidadPremiosEliminados',

                'Redimidos' => new Expression(
                    "(SELECT SUM(PremiosUtilizados) FROM BNF3_Cupon_Premios
                      INNER JOIN
                        BNF3_Oferta_Premios ON BNF3_Cupon_Premios.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id
                      INNER JOIN
                        BNF3_Oferta_Premios_Segmentos ON BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id
                      WHERE FechaRedimido IS NOT NULL 
                        AND BNF_Cliente_id = BNF3_Asignacion_Premios.BNF_Cliente_id
                        AND BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Asignacion_Premios.BNF3_Segmento_id)"),
                'Eliminado',
                'EstadoPremios',
                'FechaCreacion',
            )
        );
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento', 'Nombre', 'Apellido')
        );

        $select->where->equalTo("BNF3_Segmento_id", $segmento);
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
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(
            array(
                'id',
                'EstadoPremios',
                'CantidadPremios',
                'CantidadPremiosUsados',
                'CantidadPremiosDisponibles',
                'CantidadPremiosEliminados',
                'Eliminado',
                'Redimidos' => new Expression(
                    "(SELECT SUM(PremiosUtilizados) FROM BNF3_Cupon_Premios
                      INNER JOIN
                        BNF3_Oferta_Premios ON BNF3_Cupon_Premios.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id
                      INNER JOIN
                        BNF3_Oferta_Premios_Segmentos ON BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id
                      WHERE FechaRedimido IS NOT NULL 
                        AND BNF_Cliente_id = BNF3_Asignacion_Premios.BNF_Cliente_id
                        AND BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Asignacion_Premios.BNF3_Segmento_id)")
            )
        );
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array('NumeroDocumento', 'Nombre', 'Apellido')
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($campania)) {
            $select->where->equalTo("BNF3_Campanias.id", $campania);
        }

        if (!empty($segmento)) {
            $select->where->equalTo("BNF3_Segmentos.id", $segmento);
        }

        $select->where->equalTo("BNF3_Asignacion_Premios.EstadoPremios", (!empty($estado)) ? $estado : "Activado");

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
            $select->order("BNF3_Asignacion_Premios.id DESC");
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function saveAsignacion(AsignacionPremios $asignacion)
    {
        $data = array(
            'BNF3_Segmento_id' => $asignacion->BNF3_Segmento_id,
            'BNF_Cliente_id' => $asignacion->BNF_Cliente_id,
            'CantidadPremios' => $asignacion->CantidadPremios,
            'CantidadPremiosDisponibles' => $asignacion->CantidadPremiosDisponibles,
            'CantidadPremiosEliminados' => $asignacion->CantidadPremiosEliminados,
            'EstadoPremios' => $asignacion->EstadoPremios,
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

    public function cambiarEstadoPremiosAsignacion($asignacion, $estado, $Premios = 0)
    {
        if (!empty($Premios) && $estado == "Cancelado") {
            $data['CantidadPremiosEliminados'] = $Premios;
            $data['CantidadPremiosDisponibles'] = 0;
            $data['CantidadPremios'] = 0;
            $data['Eliminado'] = 1;
        }
        $data['EstadoPremios'] = $estado;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => $asignacion));
    }

    public function searchDocument($empresa, $campania, $documento)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->and->equalTo("BNF3_Campanias.id", $campania)
            ->and->equalTo("BNF_Cliente.NumeroDocumento", $documento)
            ->and->equalTo("BNF3_Asignacion_Premios.Eliminado", 0);

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
                AND BNF3_Segmentos.id = SP.id
                AND BNF3_Campanias.id = CP.id)";
        } elseif ($usarSegmento == 1) {
            $where = "AND BNF3_Segmentos.id = SP.id)";
        } elseif ($usarCampania == 1) {
            $where = "AND BNF3_Campanias.id = CP.id)";
        } else {
            $where = "AND BNF_Empresa.id = EP.id)";
        }

        #region Consultas
        $usuariosAsignados = "(SELECT 
                            COUNT(*) 
                    FROM BNF3_Asignacion_Premios
                            INNER JOIN
                        BNF_Cliente ON BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                            INNER JOIN
                        BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF3_Asignacion_Premios.EstadoPremios = 'Activado' " . $where;

        $PremiosAsignados = "(SELECT 
                        IFNULL(SUM(BNF3_Asignacion_Premios.CantidadPremios), 0)
                    FROM
                        BNF3_Asignacion_Premios
                            INNER JOIN
                        BNF_Cliente ON BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                            INNER JOIN
                        BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF3_Asignacion_Premios.EstadoPremios = 'Activado' " . $where;

        $usuariosAplicados = "(SELECT 
                        COUNT(CantidadPremiosUsados)
                    FROM
                        BNF3_Asignacion_Premios
                            INNER JOIN
                        BNF_Cliente ON BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                            INNER JOIN
                        BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF3_Asignacion_Premios.EstadoPremios = 'Activado'
                            AND BNF3_Asignacion_Premios.CantidadPremiosUsados > 0
                            AND BNF3_Segmentos.Eliminado = 0 
                            AND BNF3_Campanias.Eliminado = 0 " . $where;

        $PremiosAplicados = "(SELECT 
                        IFNULL(SUM(CantidadPremiosUsados), 0)
                    FROM
                        BNF3_Asignacion_Premios
                            INNER JOIN
                        BNF_Cliente ON BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                            INNER JOIN
                        BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF3_Asignacion_Premios.EstadoPremios = 'Activado'
                            AND BNF3_Asignacion_Premios.CantidadPremiosUsados > 0
                            AND BNF3_Segmentos.Eliminado = 0 
                            AND BNF3_Campanias.Eliminado = 0 " . $where;

        $usuariosRedimidos = "(SELECT 
                        COUNT(DISTINCT BNF_Cliente.id)
                    FROM
                        BNF3_Cupon_Premios
                            INNER JOIN
                        BNF3_Asignacion_Premios ON BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = BNF3_Asignacion_Premios.id
                            INNER JOIN
                        BNF_Cliente ON BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                            INNER JOIN
                        BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF3_Asignacion_Premios.EstadoPremios = 'Activado'                            
                            AND BNF3_Segmentos.Eliminado = 0 
                            AND BNF3_Campanias.Eliminado = 0
                            AND BNF3_Campanias_Empresas.Eliminado = 0
                            AND FechaRedimido IS NOT NULL " . $where;

        $PremiosRedimidos = "(SELECT 
                        IFNULL(SUM(PremiosUtilizados), 0)
                    FROM
                        BNF3_Cupon_Premios
                            INNER JOIN
                        BNF3_Asignacion_Premios ON BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = BNF3_Asignacion_Premios.id
                            INNER JOIN
                        BNF_Cliente ON BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                            INNER JOIN
                        BNF3_Campanias_Empresas ON BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id
                            INNER JOIN
                        BNF_Empresa ON BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id
                    WHERE
                        BNF3_Asignacion_Premios.EstadoPremios = 'Activado'
                            AND BNF3_Segmentos.Eliminado = 0 
                            AND BNF3_Campanias.Eliminado = 0
                            AND BNF3_Campanias_Empresas.Eliminado = 0
                            AND FechaRedimido IS NOT NULL " . $where;
        #endregion

        $select = new Select();
        $select->from(array('ASP' => 'BNF3_Asignacion_Premios'));
        $select->columns(
            array(
                'UsuAsignados' => new Expression($usuariosAsignados),
                'TotalAsignados' => new Expression($PremiosAsignados),
                'UsuAplicados' => new Expression($usuariosAplicados),
                'TotalAplicados' => new Expression($PremiosAplicados),
                'UsuRedimidos' => new Expression($usuariosRedimidos),
                'Redimidos' => new Expression($PremiosRedimidos),
                'Correos' => new Expression(
                    "(SELECT DISTINCT GROUP_CONCAT(distinct TRIM(Correo) SEPARATOR ', ') FROM BNF_ClienteCorreo " .
                    "WHERE Correo IS NOT NULL AND TRIM(Correo) != '' AND BNF_Cliente_id = CL.id)"
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
            array('SP' => 'BNF3_Segmentos'),
            'ASP.BNF3_Segmento_id = SP.id',
            array('Segmento' => 'NombreSegmento')
        );
        $select->join(
            array('CP' => 'BNF3_Campanias'),
            'SP.BNF3_Campania_id = CP.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            array('CEP' => 'BNF3_Campanias_Empresas'),
            'CP.id = CEP.BNF3_Campania_id',
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
        $select->where->equalTo("ASP.EstadoPremios", 'Activado');

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
        $select->from(array("ASP" => 'BNF3_Asignacion_Premios'));
        $select->columns(
            array(
                'Correos' => new Expression(
                    "(SELECT DISTINCT GROUP_CONCAT(distinct TRIM(Correo) SEPARATOR ', ') FROM BNF_ClienteCorreo " .
                    "WHERE Correo IS NOT NULL AND TRIM(Correo) != '' AND BNF_Cliente_id = CL.id)"
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
            array('SP' => 'BNF3_Segmentos'),
            'ASP.BNF3_Segmento_id = SP.id',
            array('Segmento' => 'NombreSegmento')
        );
        $select->join(
            array('CP' => 'BNF3_Campanias'),
            'SP.BNF3_Campania_id = CP.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            array('CEP' => 'BNF3_Campanias_Empresas'),
            'CP.id = CEP.BNF3_Campania_id',
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
        $select->where->equalTo("ASP.EstadoPremios", 'Activado');
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
                                    BNF3_Cupon_Premios.PremiosUtilizados
                                FROM
                                    BNF3_Cupon_Premios
                                WHERE
                                    BNF3_Cupon_Premios.id = COP.id
                                        AND BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = ASP.id)";
        } else {
            $totalAplicados = "
            (SELECT
                IFNULL(SUM(BNF3_Cupon_Premios.PremiosUtilizados), 0)
            FROM
                BNF3_Asignacion_Premios
                    LEFT JOIN
                BNF3_Cupon_Premios ON BNF3_Cupon_Premios.BNF_Cliente_id = BNF3_Asignacion_Premios.BNF_Cliente_id
                    LEFT JOIN
                BNF3_Oferta_Premios ON BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id
                    LEFT JOIN
                BNF3_Oferta_Premios_Rubro ON BNF3_Oferta_Premios_Rubro.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id
                    LEFT JOIN
                BNF_Rubro ON BNF_Rubro.id = BNF3_Oferta_Premios_Rubro.BNF_Rubro_id
                    INNER JOIN
                BNF3_Segmentos ON BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id
                    INNER JOIN
                BNF3_Campanias ON BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id
            WHERE
                BNF3_Cupon_Premios.BNF_Empresa_id = EP.id
                    AND BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = ASP.id
                    AND BNF3_Asignacion_Premios.Eliminado = '0'
                    AND BNF3_Asignacion_Premios.EstadoPremios = 'Activado'
                    AND BNF3_Asignacion_Premios.CantidadPremiosUsados > 0
                    AND BNF_Rubro.id = RB.id
                    AND BNF3_Segmentos.id = SP.id
                    AND BNF3_Campanias.id = CP.id)";
        }

        if ($usarUsuario) {
            $redimidos = "(SELECT 
                                IFNULL(BNF3_Cupon_Premios.PremiosUtilizados, 0)
                            FROM
                                BNF3_Cupon_Premios
                            WHERE
                                FechaRedimido IS NOT NULL
                                    AND BNF3_Cupon_Premios.id = COP.id
                                    AND BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = ASP.id)";
        } else {
            $redimidos = "
            (SELECT 
                IFNULL(SUM(PremiosUtilizados), 0)
            FROM
                BNF3_Asignacion_Premios
                    LEFT JOIN
                BNF3_Cupon_Premios ON BNF3_Cupon_Premios.BNF_Cliente_id = BNF3_Asignacion_Premios.BNF_Cliente_id
                    LEFT JOIN
                BNF3_Oferta_Premios ON BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id
                    LEFT JOIN
                BNF3_Oferta_Premios_Rubro ON BNF3_Oferta_Premios_Rubro.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id
                    LEFT JOIN
                BNF_Rubro ON BNF_Rubro.id = BNF3_Oferta_Premios_Rubro.BNF_Rubro_id
                    INNER JOIN
                BNF3_Segmentos ON BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id
                    INNER JOIN
                BNF3_Campanias ON BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id
            WHERE
                FechaRedimido IS NOT NULL
                    AND BNF3_Cupon_Premios.BNF_Empresa_id = EP.id
                    AND BNF3_Cupon_Premios.BNF3_Asignacion_Premios_id = ASP.id
                    AND BNF3_Asignacion_Premios.Eliminado = '0'
                    AND BNF3_Asignacion_Premios.EstadoPremios = 'Activado'
                    AND BNF3_Asignacion_Premios.CantidadPremiosUsados > 0
                    AND BNF_Rubro.id = RB.id
                    AND BNF3_Segmentos.id = SP.id
                    AND BNF3_Campanias.id = CP.id)";
        }

        $select = new Select();
        $select->from(array('ASP' => 'BNF3_Asignacion_Premios'));
        $select->columns(
            array(
                'TotalAplicados' => new Expression($totalAplicados),
                'Redimidos' => new Expression($redimidos)
            )
        );
        $select->join(
            array('COP' => 'BNF3_Cupon_Premios'),
            'COP.BNF_Cliente_id = ASP.BNF_Cliente_id',
            array(),
            "left"
        );
        $select->join(
            array('OF' => 'BNF3_Oferta_Premios'),
            'OF.id = COP.BNF3_Oferta_Premios_id',
            array(),
            "left"
        );
        $select->join(
            array('OFR' => 'BNF3_Oferta_Premios_Rubro'),
            'OFR.BNF3_Oferta_Premios_id = OF.id',
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
            array('SP' => 'BNF3_Segmentos'),
            'SP.id = ASP.BNF3_Segmento_id',
            array('Segmento' => 'NombreSegmento')
        );
        $select->join(
            array('CP' => 'BNF3_Campanias'),
            'SP.BNF3_Campania_id = CP.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            array('CEP' => 'BNF3_Campanias_Empresas'),
            'CP.id = CEP.BNF3_Campania_id',
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
        $select->where->equalTo("ASP.EstadoPremios", 'Activado');
        $select->where->equalTo("CP.Eliminado", 0);
        $select->where->equalTo("SP.Eliminado", 0);

        if ($usarUsuario) {
            $select->order("CP.id DESC");
            $select->order("SP.id");
            $select->order("CL.id DESC");
            $select->order("EP.id");
            $select->order("RB.id");
        } else {
            $select->quantifier('DISTINCT');
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
