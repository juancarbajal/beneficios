<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 19/04/16
 * Time: 04:53 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutCategoriaPosicionTable
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

    public function getLayoutCategoriaPosicion($idLayoutCategoria)
    {
        $select = new Select();
        $select->from('BNF_LayoutCategoriaPosicion');
        $select->columns(array('BNF_Oferta_id', 'Index'));
        $select->where->equalTo('BNF_LayoutCategoria_id', $idLayoutCategoria)
            ->AND->equalTo('Eliminado', 0);

        $select->order('BNF_LayoutCategoria_id');
        $select->order('Index');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->toArray();
    }
}
