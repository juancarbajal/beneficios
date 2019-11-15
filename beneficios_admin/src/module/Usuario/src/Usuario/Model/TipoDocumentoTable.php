<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 31/08/15
 * Time: 10:55 AM
 */

namespace Usuario\Model;

use Zend\Db\TableGateway\TableGateway;

class TipoDocumentoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(array('Eliminado' => 0));
        return $resultSet;
    }

    public function getTipoDocumento($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveTipoDocumento(TipoDocumento $system)
    {
        $data = array(//
        );

        $id = (int)$system->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTipoDocumento($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteTipoDocumento($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
