<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 31/08/15
 * Time: 01:01 AM
 */

namespace Usuario\Model;

use Zend\Db\TableGateway\TableGateway;

class TipoUsuarioTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('BNF_TipoUsuario');
        $select->columns(array('*'));
        $select->where->notEqualTo('Descripcion', 'super');
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTipoUsuario($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveTipoUsuario(System $system)
    {
        $data = array(
            //
        );

        $id = (int) $system->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTipoUsuario($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteTipoUsuario($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
