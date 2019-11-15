<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/10/15
 * Time: 07:42 PM
 */

namespace Application\Model\Table;


use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CampaniaTable
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

    public function getCampania($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Campania $id");
        }
        return $row;
    }

    public function getBuscarCampania($slug)
    {
        $rowset = $this->tableGateway->select(array('Slug' => $slug, 'Eliminado' => '0'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getBuscarCampaniaXPais($id)
    {
        $select = new Select();
        $select->from('BNF_Campanias');
        $select->join('BNF_CampaniaUbigeo', 'BNF_CampaniaUbigeo.BNF_Campanias_id = BNF_Campanias.id', array());
        $select->where
            ->equalTo('BNF_Campanias.Eliminado', '0')
            ->and
            ->equalTo('BNF_CampaniaUbigeo.BNF_Pais_id', $id);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
