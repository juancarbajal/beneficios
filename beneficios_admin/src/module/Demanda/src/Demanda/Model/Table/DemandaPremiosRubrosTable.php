<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/06/16
 * Time: 05:57 PM
 */

namespace Demanda\Model\Table;

use Demanda\Model\DemandaPremiosRubros;
use Zend\Db\TableGateway\TableGateway;

class DemandaPremiosRubrosTable
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

    public function getDemandaRubro($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getDemandaRubroByDemanda($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array("BNF3_Demanda_id" => $id, "Eliminado" => 0));
        return $resultSet;
    }

    public function getIfExist($id, $rubro)
    {
        $id = (int)$id;
        $rubro = (int)$rubro;
        $rowset = $this->tableGateway->select(array('BNF3_Demanda_id' => $id, "BNF_Rubro_id" => $rubro));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveDemandaRubro(DemandaPremiosRubros $demandaRubros)
    {
        $data = array(
            'BNF_Rubro_id' => $demandaRubros->BNF_Rubro_id,
            'BNF3_Demanda_id' => $demandaRubros->BNF3_Demanda_id,
            'Eliminado' => $demandaRubros->Eliminado,
        );

        $id = (int)$demandaRubros->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getDemandaRubro($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('DemandaRubro id does not exist');
            }
        }
    }

    public function deleteDemandaRubro($id)
    {
        $data["Eliminado"] = 1;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF3_Demanda_id' => $id));
    }

    public function updateDemandaRubros($id, $rubro)
    {
        $data["Eliminado"] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF3_Demanda_id' => $id, "BNF_Rubro_id" => $rubro));
    }
}
