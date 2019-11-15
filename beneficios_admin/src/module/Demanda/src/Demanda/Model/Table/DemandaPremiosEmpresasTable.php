<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/06/16
 * Time: 05:57 PM
 */

namespace Demanda\Model\Table;

use Demanda\Model\DemandaPremiosEmpresas;
use Zend\Db\TableGateway\TableGateway;

class DemandaPremiosEmpresasTable
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

    public function getDemandaEmpresa($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getDemandaEmpresaByDemanda($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array("BNF3_Demanda_id" => $id, "Eliminado" => 0));
        return $resultSet;
    }

    public function getIfExist($id, $empresa)
    {
        $id = (int)$id;
        $empresa = (int)$empresa;
        $rowset = $this->tableGateway->select(array("BNF3_Demanda_id" => $id, "BNF_Empresa_id" => $empresa));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveDemandaEmpresa(DemandaPremiosEmpresas $demandaEmpresas)
    {
        $data = array(
            'BNF_Empresa_id' => $demandaEmpresas->BNF_Empresa_id,
            'BNF3_Demanda_id' => $demandaEmpresas->BNF3_Demanda_id,
            'Eliminado' => $demandaEmpresas->Eliminado,
        );

        $id = (int)$demandaEmpresas->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getDemandaEmpresa($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('DemandaEmpresa id does not exist');
            }
        }
    }

    public function deleteDemandaEmpresa($id)
    {
        $data["Eliminado"] = 1;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF3_Demanda_id' => $id));
    }

    public function updateDemandaEmpresas($id, $empresa)
    {
        $data["Eliminado"] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF3_Demanda_id' => $id, "BNF_Empresa_id" => $empresa));
    }
}
