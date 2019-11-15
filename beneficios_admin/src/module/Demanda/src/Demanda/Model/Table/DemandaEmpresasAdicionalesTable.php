<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/06/16
 * Time: 12:34 AM
 */

namespace Demanda\Model\Table;

use Demanda\Model\DemandaEmpresasAdicionales;
use Zend\Db\TableGateway\TableGateway;

class DemandaEmpresasAdicionalesTable
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

    public function getDemandaEmpresaAdicional($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getDemandaEmpresaAdicionalByDemanda($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array("BNF2_Demanda_id" => $id, "Eliminado" => 0));
        return $resultSet;
    }

    public function getIfExist($id, $adicional)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array("BNF2_Demanda_id" => $id, "NombreEmpresa" => $adicional));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveDemandaEmpresasAdicionales(DemandaEmpresasAdicionales $demandaEmpresas)
    {
        $data = array(
            'NombreEmpresa' => $demandaEmpresas->NombreEmpresa,
            'BNF2_Demanda_id' => $demandaEmpresas->BNF2_Demanda_id,
            'Eliminado' => $demandaEmpresas->Eliminado,
        );

        $id = (int)$demandaEmpresas->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getDemandaEmpresaAdicional($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('DemandaEmpresa id does not exist');
            }
        }
    }

    public function deleteDemandaEmpresaAdicional($id)
    {
        $data["Eliminado"] = 1;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF2_Demanda_id' => $id));
    }

    public function updateDemandaEmpresaAdicionales($id, $adicional)
    {
        $data["Eliminado"] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF2_Demanda_id' => $id, "NombreEmpresa" => $adicional));
    }
}
