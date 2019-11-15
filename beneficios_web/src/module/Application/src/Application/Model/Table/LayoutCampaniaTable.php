<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/10/15
 * Time: 07:51 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LayoutCampaniaTable
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

    public function getLayoutCampania($id_campania, $id_empresa = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutCampania');
        $select->columns(
            array(
                'id' => 'id',
                'TipoLayout' => new Expression('BNF_Layout.id'),
                'fila' => new Expression('BNF_LayoutCampania.Index'),
            )
        );
        $select->join('BNF_Layout', 'BNF_Layout.id = BNF_LayoutCampania.BNF_Layout_id', array());
        $select->where->equalTo('BNF_LayoutCampania.BNF_Campanias_id', $id_campania);
        if ($id_empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $id_empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $select->order('BNF_LayoutCampania.Index');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
