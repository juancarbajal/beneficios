<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 30/10/15
 * Time: 21:44
 */

namespace Application\Model\Table;

use Zend\Db\TableGateway\TableGateway;

class ConfiguracionesTable
{
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(null);
        return $resultSet;
    }

    public function getConfig($campo)
    {
        $resultSet = $this->tableGateway->select(array('Campo' => $campo));
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}
