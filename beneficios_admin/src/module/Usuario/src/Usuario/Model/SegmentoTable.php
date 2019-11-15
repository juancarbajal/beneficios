<?php

namespace Usuario\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class SegmentoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        $resultSet->buffer();
        return $resultSet;
    }

    public function getSegmento($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getByNombre($nombre)
    {
        $rowset = $this->tableGateway->select(array('Nombre' => $nombre));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveSegmento(Segmento $system)
    {
        $data = array(
            //
        );

        $id = (int) $system->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSegmento($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteSegmento($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
