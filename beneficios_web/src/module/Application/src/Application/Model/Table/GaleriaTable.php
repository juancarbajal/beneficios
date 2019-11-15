<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/10/15
 * Time: 10:57 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class GaleriaTable
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

    public function getGalerias($empresa = null)
    {
        $select = new Select();
        $select->from('BNF_Galeria');
        $select->where->equalTo('Eliminado', '0');
        if ($empresa != null) {
            $select->where->equalTo('BNF_Empresa_id', $empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $resultSet = $this->tableGateway->selectWith($select);
        if (!count($resultSet)) {
            $select = new Select();
            $select->from('BNF_Galeria');
            $select->where->equalTo('Eliminado', '0')
                ->and->isNull('BNF_Empresa_id');
            $resultSet = $this->tableGateway->selectWith($select);
        }
        return $resultSet;
    }

    public function getGaleria($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
}