<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 13/11/15
 * Time: 11:24
 */

namespace Auth\Model\Table;


use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class ClienteCorreoTable
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

    public function getCorreos($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_ClienteCorreo');
        $select->where->equalTo('BNF_Cliente_id', $id);
        $select->order('FechaActualizacion DESC');
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->count();
        if ($row == 0) {
            return false;
        }
        return $resultSet;
    }

    public function buscarCorreo($email, $idCliente)
    {
        $rowset = $this->tableGateway->select(array('Correo' => $email, 'BNF_Cliente_id' => $idCliente));
        $row = $rowset->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCorreo($idCliente, $correo)
    {
        $data = array();
        $data['Correo'] = $correo;
        $data['BNF_Cliente_id'] = $idCliente;
        $data['FechaCreacion'] = date("Y-m-d H:i:s");
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $data['Eliminado'] = '0';
        $this->tableGateway->insert($data);
        $id = $this->tableGateway->getLastInsertValue();
        return $id;
    }

    public function updateCorreo($id)
    {
        $data = array();
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => $id));
        $id = $this->tableGateway->getLastInsertValue();
        return $id;
    }

    public function getUltimoCorreo($id)
    {
        $select = new Select();
        $select->from('BNF_ClienteCorreo');
        $select->where->equalTo('BNF_Cliente_id', $id);
        $select->order('FechaActualizacion DESC');
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->count();
        if ($row == 0) {
            return false;
        }
        return $resultSet->current();
    }
}