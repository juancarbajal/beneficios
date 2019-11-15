<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 04/09/15
 * Time: 03:50 PM
 */

namespace Empresa\Model;

use Zend\Db\Sql\Select;
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

    public function fetchAllProvince()
    {
        $resultSet = $this->tableGateway->select('id_padre is not null');
        return $resultSet;
    }

    public function getUbigeo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveUbigeo(Ubigeo $ubigeo)
    {
        $data = array(
            'Nombre' => $ubigeo->Nombre,
            'id_Padre' => $ubigeo->id_padre,
            'BNF_Pais_id' => $ubigeo->BNF_Pais_id
        );

        $id = (int)$ubigeo->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Ubigeo id does not exist');
            }
        }
    }

    public function deleteUbigeo($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function getLocalizacion($ubigeo)
    {
        $select = new Select();
        $select->from(array('prov' => 'BNF_Ubigeo'));
        $select->columns(array('Provincia' => 'Nombre'));
        $select->join(
            array('dep' => 'BNF_Ubigeo'),
            'dep.id = prov.id_padre',
            array('Departamento' => 'Nombre')
        );
        $select->join(
            array('pais' => 'BNF_Pais'),
            'pais.id = dep.BNF_Pais_id ',
            array('Pais' => 'NombrePais')
        );
        $select->where->equalTo('prov.id', $ubigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDepartamentPais($pais)
    {
        $select = new Select();
        $select->from('BNF_Ubigeo');
        $select->where('BNF_Pais_id = ' . $pais);
        $select->where('id_padre is null');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDepartamentPaisCount($pais)
    {
        $select = new Select();
        $select->from('BNF_Ubigeo');
        $select->where('BNF_Pais_id = ' . $pais);
        $select->where('id_padre is null');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }
}
