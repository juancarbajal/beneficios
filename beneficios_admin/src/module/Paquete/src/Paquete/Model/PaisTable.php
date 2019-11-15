<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/09/15
 * Time: 01:18 AM
 */

namespace Paquete\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class PaisTable
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

    public function getPais($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getPaisByDepartament($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Pais');
        $select->join(
            'BNF_Ubigeo',
            'BNF_Pais.id = BNF_Ubigeo.BNF_Pais_id',
            array()
        );
        $select->where->equalTo('BNF_Ubigeo.id', $id);

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->toArray();
    }

    public function savePais(Pais $pais)
    {
        $data = array(
            //
        );

        $id = (int) $pais->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPais($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Pais id does not exist');
            }
        }
    }

    public function deletePais($id, $val)
    {
        $data['Eliminado']=$val;
        $this->tableGateway->update($data, array('id' => (int) $id));
    }
}
