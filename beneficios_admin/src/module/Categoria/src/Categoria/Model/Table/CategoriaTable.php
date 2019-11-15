<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 10/09/15
 * Time: 04:50 PM
 */

namespace Categoria\Model\Table;

use Categoria\Model\Categoria;
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

    public function getCategoriabyName($nombre, $id = "")
    {
        if ($id == "") {
            $rowset = $this->tableGateway->select(array('Nombre' => $nombre));
        } else {
            $rowset = $this->tableGateway->select(array('Nombre' => $nombre, 'id != ' . $id));
        }
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function getCategoriaBySlug($slug)
    {
        $rowset = $this->tableGateway->select(array('Slug' => $slug));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCategoriaPais($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Categoria');
        $select->columns(array('id', 'Nombre'));
        $select->join('BNF_CategoriaUbigeo', 'BNF_CategoriaUbigeo.BNF_Categoria_id=BNF_Categoria.id', array());
        $select->where->equalTo('BNF_CategoriaUbigeo.BNF_Pais_id', $id);
        $select->where("BNF_Categoria.Eliminado = '0' AND BNF_CategoriaUbigeo.Eliminado = '0'");
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCategoriaEdit($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Categoria');
        $select->columns(array('*'));
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF_CategoriaUbigeo.BNF_Categoria_id=BNF_Categoria.id',
            array('CU_id' => 'id')
        );
        $select->join('BNF_Pais', 'BNF_Pais.id = BNF_CategoriaUbigeo.BNF_Pais_id', array('NombrePais' => 'id'));
        $select->where->equalTo('BNF_Categoria.id', $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCategoriaDetails($pais = "", $nombre = "", $order_by = "", $order = "")
    {
        $select = new Select();
        $select->from('BNF_Categoria');
        $select->join('BNF_CategoriaUbigeo', 'BNF_Categoria.id = BNF_CategoriaUbigeo.BNF_Categoria_id', array());
        $select->join('BNF_Pais', 'BNF_CategoriaUbigeo.BNF_Pais_id = BNF_Pais.id', array('NombrePais', 'Pais' => 'id'));
        if ($pais != "" and $nombre != "") {
            $select->where("BNF_Pais.id = " . $pais . " OR BNF_Categoria.Nombre like '%" . $nombre . "%'");
        } elseif ($pais != "" and $nombre == "") {
            $select->where("BNF_Pais.id = " . $pais);
        } else {
            $select->where("BNF_Categoria.Nombre like '%" . $nombre . "%'");
        }
        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF_Categoria.id $order");
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function saveCategoria(Categoria $categoria)
    {
        $data = $categoria->getArrayCopy();
        unset($data['NombrePais']);
        unset($data['CU_id']);
        $id = (int)$categoria->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getCategoria($id)) {
                unset($data['FechaCreacion']);
                unset($data['Eliminado']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El id de la Categoria no existe.');
            }
        }
        return $id;
    }

    public function deleteCategoria($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getCategoriaIds()
    {
        $select = new Select();
        $select->from('BNF_Categoria');
        $select->where->equalTo('Eliminado', '0');
        $select->order('id');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
