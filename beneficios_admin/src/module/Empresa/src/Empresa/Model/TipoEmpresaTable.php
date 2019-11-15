<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:29 PM
 */

namespace Empresa\Model;


use Zend\Db\TableGateway\TableGateway;

class TipoEmpresaTable
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

    public function getTipoEmpresa($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
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

    public function getAllTipoEmpresa($id)
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('BNF_TipoEmpresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array('BNF_TipoEmpresa.id' => 'id')
        );
        $select->where
            ->equalTo("BNF_EmpresaTipoEmpresa.BNF_Empresa_id", $id)
            ->and
            ->equalTo('BNF_EmpresaTipoEmpresa.Eliminado', '0');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveTipoEmpresa(TipoEmpresa $system)
    {
        $data = array(//
        );

        $id = (int)$system->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTipoEmpresa($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteTipoEmpresa($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
