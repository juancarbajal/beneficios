<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/08/16
 * Time: 11:36 AM
 */

namespace Application\Model\Table;

use Application\Model\AsignacionEstadoLog;
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

    public function saveAsignacionEstadoLog(AsignacionEstadoLog $asignacionEstadoLog)
    {
        $data = array(
            'BNF2_Asignacion_Puntos_id' => $asignacionEstadoLog->BNF2_Asignacion_Puntos_id,
            'BNF2_Segmento_id' => $asignacionEstadoLog->BNF2_Segmento_id,
            'BNF_Cliente_id' => $asignacionEstadoLog->BNF_Cliente_id,
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
}
