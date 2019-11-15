<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 10/09/15
 * Time: 04:50 PM
 */

namespace Application\Model\Table;

use Application\Model\Categoria;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CategoriaTable
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

    public function getCategoria($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Categoria $id");
        }
        return $row;
    }

    public function getBuscarCategoria($slug)
    {
        $rowset = $this->tableGateway->select(array('Slug' => $slug));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getBuscarCategoriaXPais($id)
    {
        $select = new Select();
        $select->from('BNF_Categoria');
        $select->join('BNF_CategoriaUbigeo', 'BNF_CategoriaUbigeo.BNF_Categoria_id = BNF_Categoria.id', array());
        $select->where
            ->equalTo('BNF_Categoria.Eliminado', '0')
            ->and
            ->equalTo('BNF_CategoriaUbigeo.BNF_Pais_id', $id);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getBuscarCatOtros($pais)
    {
        $select = new Select();
        $select->from('BNF_Categoria');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF_CategoriaUbigeo.BNF_Categoria_id = BNF_Categoria.id',
            array()
        );
        $select->where
            ->equalTo('BNF_Categoria.Eliminado', '0')
            ->and
            ->equalTo('BNF_CategoriaUbigeo.BNF_Pais_id', $pais)
            ->and
            ->equalTo('BNF_Categoria.Slug','otros');
        $resultSet = $this->tableGateway->selectWith($select);
        return ($resultSet->current()!=null)?1:0;
    }
}
