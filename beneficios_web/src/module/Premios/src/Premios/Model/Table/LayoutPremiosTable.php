<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:31 PM
 */

namespace Premios\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutPremiosTable
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

    public function getLayoutPremios($id_empresa = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutPremios');
        $select->columns(
            array(
                'id' => 'id',
                'TipoLayout' => new Expression('BNF_Layout.id'),
                'fila' => new Expression('BNF_LayoutPremios.INDEX'),
            )
        );
        $select->join('BNF_Layout', 'BNF_Layout.id = BNF_LayoutPremios.BNF_Layout_id', array());

        if ($id_empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $id_empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $select->order('BNF_LayoutPremios.INDEX');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}