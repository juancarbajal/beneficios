<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/08/16
 * Time: 11:52 AM
 */

namespace Puntos\Model\Table;

use Puntos\Model\CampaniaPLog;
use Zend\Db\TableGateway\TableGateway;

class CampaniaPLogTable
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

    public function getCampaniaPLog($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCampaniaPLogByCampania($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF2_Campania_id' => $id));
        $row = $rowSet->buffer();
        if (!$row) {
            return false;
        }
        foreach ($rowSet->buffer() as $value) {
            $row = $value;
        }
        return $row;
    }

    public function saveCampaniaPLog(CampaniaPLog $campaniaPLog)
    {
        $data = array(
            'BNF2_Campania_id' => $campaniaPLog->BNF2_Campania_id,
            'NombreCampania' => $campaniaPLog->NombreCampania,
            'TipoSegmento' => $campaniaPLog->TipoSegmento,
            'FechaCampania' => $campaniaPLog->FechaCampania,
            'VigenciaInicio' => $campaniaPLog->VigenciaInicio,
            'VigenciaFin' => $campaniaPLog->VigenciaFin,
            'PresupuestoNegociado' => $campaniaPLog->PresupuestoNegociado,
            'PresupuestoAsignado' => $campaniaPLog->PresupuestoAsignado,
            'ParametroAlerta' => $campaniaPLog->ParametroAlerta,
            'Comentario' => $campaniaPLog->Comentario,
            'Relacionado' => $campaniaPLog->Relacionado,
            'EstadoCampania' => $campaniaPLog->EstadoCampania,
            'BNF_Empresa_id' => $campaniaPLog->BNF_Empresa_id,
            'Segmentos' => $campaniaPLog->Segmentos,
            'RazonEliminado' => $campaniaPLog->RazonEliminado,
        );

        $id = (int)$campaniaPLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('CampaniaPLog id no create');
        }
    }
}
