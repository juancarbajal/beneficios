<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/09/15
 * Time: 12:35 AM
 */
namespace Paquete\Model;

use Zend\Db\TableGateway\TableGateway;

class TipoPaqueteTable
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

    public function getTipoPaquete($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveTipoPaquete(TipoPaquete $tipoPaquete)
    {
        $data = array(//
        );

        $id = (int)$tipoPaquete->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTipoPaquete($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('TipoPaquete id does not exist');
            }
        }
    }

    public function deleteTipoPaquete($id, $val)
    {
        $data['Eliminado'] = $val;
        $this->tableGateway->update($data, array('id' => (int)$id));
    }
}
