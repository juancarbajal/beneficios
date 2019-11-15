<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 22/09/15
 * Time: 11:23 AM
 */

namespace Oferta\Model\Table;

use Zend\Db\TableGateway\TableGateway;

class TipoBeneficioTable
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
}
