<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/08/16
 * Time: 11:52 AM
 */

namespace Premios\Model\Table;

use Premios\Model\CampaniaPremiosLog;
use Zend\Db\TableGateway\TableGateway;

class CampaniaPremiosLogTable
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

    public function getCampaniaPremiosLog($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCampaniaPremiosLogByCampania($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF3_Campania_id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCampaniaPremiosLog(CampaniaPremiosLog $CampaniaPremiosLog)
    {
        $data = array(
            'BNF3_Campania_id' => $CampaniaPremiosLog->BNF3_Campania_id,
            'NombreCampania' => $CampaniaPremiosLog->NombreCampania,
            'TipoSegmento' => $CampaniaPremiosLog->TipoSegmento,
            'FechaCampania' => $CampaniaPremiosLog->FechaCampania,
            'VigenciaInicio' => $CampaniaPremiosLog->VigenciaInicio,
            'VigenciaFin' => $CampaniaPremiosLog->VigenciaFin,
            'PresupuestoNegociado' => $CampaniaPremiosLog->PresupuestoNegociado,
            'PresupuestoAsignado' => $CampaniaPremiosLog->PresupuestoAsignado,
            'ParametroAlerta' => $CampaniaPremiosLog->ParametroAlerta,
            'Comentario' => $CampaniaPremiosLog->Comentario,
            'Relacionado' => $CampaniaPremiosLog->Relacionado,
            'EstadoCampania' => $CampaniaPremiosLog->EstadoCampania,
            'BNF_Empresa_id' => $CampaniaPremiosLog->BNF_Empresa_id,
            'Segmentos' => $CampaniaPremiosLog->Segmentos,
            'RazonEliminado' => $CampaniaPremiosLog->RazonEliminado,
        );

        $id = (int)$CampaniaPremiosLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('CampaniaPremiosLog id no create');
        }
    }
}
