<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 04/09/15
 * Time: 03:50 PM
 */

namespace Application\Model\Table;

use Zend\Db\TableGateway\TableGateway;

class UbigeoTable
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

    public function fetchAllDepartament()
    {
        $resultSet = $this->tableGateway->select('id_padre is null');
        return $resultSet;
    }

    public function fetchAllDepartamentXPais($pais)
    {
        $resultSet = $this->tableGateway->select('id_padre is null','BNF_Pais_id ='.$pais);
        return $resultSet;
    }

    public function fetchAllProvince()
    {
        $resultSet = $this->tableGateway->select('id_padre is not null');
        return $resultSet;
    }

    public function getUbigeo($id = 14)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function deleteUbigeo($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function getDepartamentPais($pais)
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('BNF_Ubigeo');
        $select->where('BNF_Pais_id = '.$pais);
        $select->where('id_padre is null');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDepartamentPaisCount($pais)
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('BNF_Ubigeo');
        //$select->columns(array('id' => 'BNF_Empresa_id','Nombre' => 'Nombre'));
        $select->where('BNF_Pais_id = '.$pais);
        $select->where('id_padre is null');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }
}
