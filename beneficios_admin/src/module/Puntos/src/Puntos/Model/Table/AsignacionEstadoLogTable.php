<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/08/16
 * Time: 11:36 AM
 */

namespace Puntos\Model\Table;

use Puntos\Model\AsignacionEstadoLog;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AsignacionEstadoLogTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllAsignacionEstadoLog()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getAsignacionEstadoLog($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getAsignacionEstadoLogByAsignacionId($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF2_Asignacion_Puntos_id' => $id));
        return $rowSet;
    }

    public function getPuntosFinalizados()
    {
        $fecha = date('Y-m-d');
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos_Estado_Log');
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id',
            array('*')
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias_Empresas.BNF2_Campania_id = BNF2_Segmentos.BNF2_Campania_id   ',
            array()
        );

        $select->where->equalTo("Operacion", "Asignar")
            ->AND->equalTo("Motivo", "Agregando Puntos Referido")
            ->AND->equalTo("Estado_Cron", 0)
            ->AND->equalTo("BNF2_Campanias_Empresas.BNF_Empresa_id ", 369)
            ->AND->literal("TIMESTAMPDIFF(DAY, '" . $fecha . "', DATE(BNF2_Asignacion_Puntos_Estado_Log.FechaCreacion)) < 0");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getAsignacionReferidosLog($asignacion_id, $segmento_id)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos_Estado_Log');
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias_Empresas.BNF2_Campania_id = BNF2_Segmentos.BNF2_Campania_id   ',
            array()
        );

        $select->where->equalTo("Operacion", "Asignar")
            ->AND->equalTo("Estado_Cron", 0)
            ->AND->equalTo("BNF2_Campanias_Empresas.BNF_Empresa_id ", 369)
            ->AND->equalTo('BNF2_Asignacion_Puntos_id', $asignacion_id)
            ->AND->equalTo('BNF2_Segmento_id', $segmento_id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveAsignacionEstadoLog(AsignacionEstadoLog $asignacionEstadoLog)
    {
        $data = array(
            'BNF2_Asignacion_Puntos_id' => $asignacionEstadoLog->BNF2_Asignacion_Puntos_id,
            'BNF2_Segmento_id' => $asignacionEstadoLog->BNF2_Segmento_id,
            'BNF_Cliente_id' => $asignacionEstadoLog->BNF_Cliente_id,
            'TipoAsignamiento' => $asignacionEstadoLog->TipoAsignamiento,
            'CantidadPuntos' => $asignacionEstadoLog->CantidadPuntos,
            'CantidadPuntosUsados' => $asignacionEstadoLog->CantidadPuntosUsados,
            'CantidadPuntosDisponibles' => $asignacionEstadoLog->CantidadPuntosDisponibles,
            'CantidadPuntosEliminados' => $asignacionEstadoLog->CantidadPuntosEliminados,
            'EstadoPuntos' => $asignacionEstadoLog->EstadoPuntos,
            'Operacion' => $asignacionEstadoLog->Operacion,
            'Puntos' => $asignacionEstadoLog->Puntos,
            'Motivo' => $asignacionEstadoLog->Motivo,
            'BNF_Usuario_id' => $asignacionEstadoLog->BNF_Usuario_id
        );

        $id = (int)$asignacionEstadoLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('AsignacionEstadoLog id no create');
        }
    }

    public function updateAsignacionEstadoLog($data, $id)
    {
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }

}
