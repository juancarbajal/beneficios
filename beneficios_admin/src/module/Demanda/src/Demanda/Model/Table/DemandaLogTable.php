<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 27/06/16
 * Time: 06:36 PM
 */

namespace Demanda\Model\Table;

use Demanda\Model\DemandaLog;
use Zend\Db\TableGateway\TableGateway;

class DemandaLogTable
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

    public function getDemandaLog($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveDemandaLog(DemandaLog $demandaLog)
    {
        $data = array(
            'BNF2_Demanda_id' => $demandaLog->BNF2_Demanda_id,
            'BNF_Empresa_id' => $demandaLog->BNF_Empresa_id,
            'FechaDemanda' => $demandaLog->FechaDemanda,
            'PrecioMinimo' => $demandaLog->PrecioMinimo,
            'PrecioMaximo' => $demandaLog->PrecioMaximo,
            'Target' => $demandaLog->Target,
            'Comentarios' => $demandaLog->Comentarios,
            'Actualizaciones' => $demandaLog->Actualizaciones,
            'Eliminado' => $demandaLog->Eliminado,
            'Rubros' => $demandaLog->Rubros,
            'Segmentos' => $demandaLog->Segmentos,
            'EmpresaProveedor' => $demandaLog->EmpresaProveedor,
            'EmpresasAdicionales' => $demandaLog->EmpresasAdicionales,
            'Departamentos' => $demandaLog->Departamentos,
        );

        $id = (int)$demandaLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('DemandaLog id no create');
        }
    }
}
