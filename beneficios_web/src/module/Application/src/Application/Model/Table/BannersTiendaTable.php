<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/11/15
 * Time: 04:45 PM
 */

namespace Application\Model\Table;


use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class BannersTiendaTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select("Eliminado = '0'");
        return $resultSet;
    }

    public function getBanners($empresa = null)
    {
        $select = new Select();
        $select->from('BNF_BannersTienda');
        $select->where->equalTo('Eliminado', '0');
        if ($empresa != null) {
            $select->where->equalTo('BNF_Empresa_id', $empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $resultSet = $this->tableGateway->selectWith($select);
        if (!count($resultSet)) {
            $select = new Select();
            $select->from('BNF_BannersTienda');
            $select->where->equalTo('Eliminado', '0')
                ->and->isNull('BNF_Empresa_id');
            $resultSet = $this->tableGateway->selectWith($select);
        }
        return $resultSet;
    }
}
