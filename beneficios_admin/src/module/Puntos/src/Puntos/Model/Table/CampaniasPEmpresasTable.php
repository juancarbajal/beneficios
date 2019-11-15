<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:29 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\CampaniasPEmpresas;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CampaniasPEmpresasTable
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

    public function getCampaniasPEmpresas($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCampaniasPEmpresasActual($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id,  "Eliminado" => 0));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getIfExist($id)
    {
        $id = (int)$id;
        try {
            $rowset = $this->tableGateway->select(array('id' => $id));
            $row = $rowset->current();
            if (!$row) {
                throw new \Exception("Could not find row $id");
            }
            return $row;
        } catch (\Exception $ex) {
            return 0;
        }
    }

    public function getbyCampaniasP($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF2_Campania_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }


    public function saveCampaniasPEmpresas(CampaniasPEmpresas $campaniasPEmpresas)
    {
        $data = array(
            'BNF2_Campania_id' => $campaniasPEmpresas->BNF2_Campania_id,
            'BNF_Empresa_id' => $campaniasPEmpresas->BNF_Empresa_id,
            'Eliminado' => $campaniasPEmpresas->Eliminado,
        );

        $id = (int)$campaniasPEmpresas->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getCampaniasPEmpresas($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('CampaniasPEmpresas id does not exist');
            }
        }
        return $id;
    }

    public function deleteCampaniasPEmpresas($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
