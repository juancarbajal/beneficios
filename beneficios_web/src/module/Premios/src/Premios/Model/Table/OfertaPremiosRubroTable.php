<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/16
 * Time: 04:34 PM
 */

namespace Premios\Model\Table;

use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosRubroTable
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

    public function getOfertaPremiosRubro($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getOfertaPremiosRubroByOferta($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id, "Eliminado" => 0));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}