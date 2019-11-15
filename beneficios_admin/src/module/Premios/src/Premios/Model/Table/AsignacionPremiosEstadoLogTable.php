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

    public function fetchAllAsignacionPremiosEstadoLog()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getAsignacionPremiosEstadoLog($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getAsignacionPremiosEstadoLogByAsignacionId($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF3_Asignacion_Premios_id' => $id));
        return $rowSet;
    }

    public function saveAsignacionPremiosEstadoLog(AsignacionPremiosEstadoLog $AsignacionPremiosEstadoLog)
    {
        $data = array(
            'BNF3_Asignacion_Premios_id' => $AsignacionPremiosEstadoLog->BNF3_Asignacion_Premios_id,
            'BNF3_Segmento_id' => $AsignacionPremiosEstadoLog->BNF3_Segmento_id,
            'BNF_Cliente_id' => $AsignacionPremiosEstadoLog->BNF_Cliente_id,
            'CantidadPremios' => $AsignacionPremiosEstadoLog->CantidadPremios,
            'CantidadPremiosUsados' => $AsignacionPremiosEstadoLog->CantidadPremiosUsados,
            'CantidadPremiosDisponibles' => $AsignacionPremiosEstadoLog->CantidadPremiosDisponibles,
            'CantidadPremiosEliminados' => $AsignacionPremiosEstadoLog->CantidadPremiosEliminados,
            'EstadoPremios' => $AsignacionPremiosEstadoLog->EstadoPremios,
            'Operacion' => $AsignacionPremiosEstadoLog->Operacion,
            'Premios' => $AsignacionPremiosEstadoLog->Premios,
            'Motivo' => $AsignacionPremiosEstadoLog->Motivo,
            'BNF_Usuario_id' => $AsignacionPremiosEstadoLog->BNF_Usuario_id
        );

        $id = (int)$AsignacionPremiosEstadoLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('AsignacionPremiosEstadoLog id no create');
        }
    }
}
