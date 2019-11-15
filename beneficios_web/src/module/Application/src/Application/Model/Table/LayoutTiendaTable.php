<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 28/10/15
 * Time: 03:47 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutTiendaTable
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

    public function getLayoutTienda($id_empresa = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutTienda');
        $select->columns(
            array(
                'id' => 'id',
                'TipoLayout' => new Expression('BNF_Layout.id'),
                'fila' => new Expression('BNF_LayoutTienda.INDEX'),
            )
        );
        $select->join('BNF_Layout', 'BNF_Layout.id = BNF_LayoutTienda.BNF_Layout_id', array());
        if ($id_empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $id_empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }

        $select->order('BNF_LayoutTienda.INDEX');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
