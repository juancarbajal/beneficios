<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:29 PM
 */

namespace Premios\Model\Table;

use Premios\Model\CampaniasPremiosEmpresas;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CampaniasPremiosEmpresasTable
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

    public function getCampaniasPremiosEmpresas($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCampaniasPremiosEmpresasActual($id)
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
        $rowset = $this->tableGateway->select(array('BNF3_Campania_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }


    public function saveCampaniasPremiosEmpresas(CampaniasPremiosEmpresas $CampaniasPremiosEmpresas)
    {
        $data = array(
            'BNF3_Campania_id' => $CampaniasPremiosEmpresas->BNF3_Campania_id,
            'BNF_Empresa_id' => $CampaniasPremiosEmpresas->BNF_Empresa_id,
            'Eliminado' => $CampaniasPremiosEmpresas->Eliminado,
        );

        $id = (int)$CampaniasPremiosEmpresas->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getCampaniasPremiosEmpresas($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('CampaniasPremiosEmpresas id does not exist');
            }
        }
        return $id;
    }

    public function deleteCampaniasPremiosEmpresas($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
