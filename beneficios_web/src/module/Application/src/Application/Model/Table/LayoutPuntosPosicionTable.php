<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:34 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutPuntosPosicionTable
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

    public function getLayoutPuntosPosicion($idLayoutCategoria)
    {
        $select = new Select();
        $select->from('BNF_LayoutPuntosPosicion');
        $select->columns(array('BNF2_Oferta_Puntos_id', 'Index'));
        $select->where->equalTo('BNF_LayoutPuntos_id', $idLayoutCategoria)
            ->AND->equalTo('Eliminado', 0);

        $select->order('BNF_LayoutPuntos_id');
        $select->order('Index');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->toArray();
    }
}