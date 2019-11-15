<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:31 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutPuntosTable
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

    public function getLayoutPuntos($id_empresa = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutPuntos');
        $select->columns(
            array(
                'id' => 'id',
                'TipoLayout' => new Expression('BNF_Layout.id'),
                'fila' => new Expression('BNF_LayoutPuntos.INDEX'),
            )
        );
        $select->join('BNF_Layout', 'BNF_Layout.id = BNF_LayoutPuntos.BNF_Layout_id', array());

        if ($id_empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $id_empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $select->order('BNF_LayoutPuntos.INDEX');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}