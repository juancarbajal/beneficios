<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 19/04/16
 * Time: 04:54 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutTiendaPosicionTable
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

    public function getLayoutTiendaPosicion($idLayoutCategoria)
    {
        $select = new Select();
        $select->from('BNF_LayoutTiendaPosicion');
        $select->columns(array('BNF_Oferta_id', 'Index'));
        $select->where->equalTo('BNF_LayoutTienda_id', $idLayoutCategoria)
            ->AND->equalTo('Eliminado', 0);

        $select->order('BNF_LayoutTienda_id');
        $select->order('Index');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->toArray();
    }
}
