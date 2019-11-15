<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/16
 * Time: 04:34 PM
 */

namespace Application\Model\Table;

use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosRubroTable
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

    public function getOfertaPuntosRubro($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getOfertaPuntosRubroByOferta($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id, "Eliminado" => 0));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}