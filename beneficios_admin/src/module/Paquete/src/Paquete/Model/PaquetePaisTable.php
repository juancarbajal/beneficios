<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/09/15
 * Time: 11:21 AM
 */

namespace Paquete\Model;

use Zend\Db\TableGateway\TableGateway;

class PaquetePaisTable
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

    public function getPaquetePais($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getPaquetePaisP($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('BNF_Paquete_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePaquetePais(PaquetePais $paquetePais)
    {
        $data = array(
            'BNF_Paquete_id'=>$paquetePais->BNF_Paquete_id,
            'BNF_Pais_id'=>$paquetePais->BNF_Pais_id,
        );

        $id = (int) $paquetePais->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id=$this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getPaquetePais($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Paquete id does not exist');
            }
        }
    }
}
