<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 15/10/15
 * Time: 19:24
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutCategoriaTable
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

    public function getLayoutCategoria($id_categoria, $id_empresa = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutCategoria');
        $select->columns(
            array(
                'id' => 'id',
                'TipoLayout' => new Expression('BNF_Layout.id'),
                'fila' => new Expression('BNF_LayoutCategoria.INDEX'),
            )
        );
        $select->join('BNF_Layout', 'BNF_Layout.id = BNF_LayoutCategoria.BNF_Layout_id', array());
        $select->where->equalTo('BNF_LayoutCategoria.BNF_Categoria_id', $id_categoria);
        if ($id_empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $id_empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $select->order('BNF_LayoutCategoria.INDEX');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
