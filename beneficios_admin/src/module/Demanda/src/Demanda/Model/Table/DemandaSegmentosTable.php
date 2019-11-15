<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/06/16
 * Time: 05:57 PM
 */

namespace Demanda\Model\Table;

use Demanda\Model\DemandaSegmentos;
use Zend\Db\TableGateway\TableGateway;

class DemandaSegmentosTable
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

    public function getDemandaSegmentos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getDemandaSegmentosByDemanda($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array("BNF2_Demanda_id" => $id, "Eliminado" => 0));
        return $resultSet;
    }

    public function getIfExist($id, $departamento)
    {
        $id = (int)$id;
        $departamento = (int)$departamento;
        $rowset = $this->tableGateway->select(array('BNF2_Demanda_id' => $id, "BNF2_Segmento_id" => $departamento));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveDemandaSegmentos(DemandaSegmentos $demandaSegmentos)
    {
        $data = array(
            'BNF2_Segmento_id' => $demandaSegmentos->BNF2_Segmento_id,
            'BNF2_Demanda_id' => $demandaSegmentos->BNF2_Demanda_id,
            'Eliminado' => $demandaSegmentos->Eliminado,
        );

        $id = (int)$demandaSegmentos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getDemandaSegmentos($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Demanda Segmentos id does not exist');
            }
        }
    }

    public function deleteDemandaSegmentos($id)
    {
        $data["Eliminado"] = 1;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF2_Demanda_id' => $id));
    }

    public function updateDemandaSegmentos($id, $segmento)
    {
        $data["Eliminado"] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF2_Demanda_id' => $id, "BNF2_Segmento_id" => $segmento));
    }
}
