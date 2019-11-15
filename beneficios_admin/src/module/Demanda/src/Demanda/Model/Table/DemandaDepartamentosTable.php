<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/06/16
 * Time: 05:57 PM
 */

namespace Demanda\Model\Table;

use Demanda\Model\DemandaDepartamentos;
use Zend\Db\TableGateway\TableGateway;

class DemandaDepartamentosTable
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

    public function getDemandaDepartamentos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getDemandaDepartamentoByDemanda($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array("BNF2_Demanda_id" => $id, "Eliminado" => 0));
        return $resultSet;
    }

    public function getIfExist($id, $departamento)
    {
        $id = (int)$id;
        $departamento = (int)$departamento;
        $rowset = $this->tableGateway->select(array('BNF2_Demanda_id' => $id, "BNF_Departamentos_id" => $departamento));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveDemandaDepartamento(DemandaDepartamentos $demandaDepartamentos)
    {
        $data = array(
            'BNF_Departamentos_id' => $demandaDepartamentos->BNF_Departamentos_id,
            'BNF2_Demanda_id' => $demandaDepartamentos->BNF2_Demanda_id,
            'Eliminado' => $demandaDepartamentos->Eliminado,
        );

        $id = (int)$demandaDepartamentos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getDemandaDepartamentos($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Demanda Departamentos id does not exist');
            }
        }
    }

    public function deleteDemandaDepartamentos($id)
    {
        $data["Eliminado"] = 1;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF2_Demanda_id' => $id));
    }

    public function updateDemandaDepartamentos($id, $departamento)
    {
        $data["Eliminado"] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF2_Demanda_id' => $id, "BNF_Departamentos_id" => $departamento));
    }
}
