<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:34 PM
 */

namespace Premios\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutPremiosPosicionTable
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

    public function getLayoutPremiosPosicion($idLayoutCategoria)
    {
        $select = new Select();
        $select->from('BNF_LayoutPremiosPosicion');
        $select->columns(array('BNF3_Oferta_Premios_id', 'Index'));
        $select->where->equalTo('BNF_LayoutPremios_id', $idLayoutCategoria)
            ->AND->equalTo('Eliminado', 0);

        $select->order('BNF_LayoutPremios_id');
        $select->order('Index');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->toArray();
    }
}