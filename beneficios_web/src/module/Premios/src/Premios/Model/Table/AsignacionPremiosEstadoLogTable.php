<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/08/16
 * Time: 11:36 AM
 */

namespace Premios\Model\Table;

use Premios\Model\AsignacionPremiosEstadoLog;
use Zend\Db\TableGateway\TableGateway;

class AsignacionPremiosEstadoLogTable
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
        $rowSet = $this->tableGateway->select(array('BNF3_Asignacion_Premios_id' => $id));
        return $rowSet;
    }

    public function saveAsignacionEstadoLog(AsignacionPremiosEstadoLog $asignacionEstadoLog)
    {
        $data = array(
            'BNF3_Asignacion_Premios_id' => $asignacionEstadoLog->BNF3_Asignacion_Premios_id,
            'BNF3_Segmento_id' => $asignacionEstadoLog->BNF3_Segmento_id,
            'BNF_Cliente_id' => $asignacionEstadoLog->BNF_Cliente_id,
            'CantidadPremios' => $asignacionEstadoLog->CantidadPremios,
            'CantidadPremiosUsados' => $asignacionEstadoLog->CantidadPremiosUsados,
            'CantidadPremiosDisponibles' => $asignacionEstadoLog->CantidadPremiosDisponibles,
            'CantidadPremiosEliminados' => $asignacionEstadoLog->CantidadPremiosEliminados,
            'EstadoPremios' => $asignacionEstadoLog->EstadoPremios,
            'Operacion' => $asignacionEstadoLog->Operacion,
            'Premios' => $asignacionEstadoLog->Premios,
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
