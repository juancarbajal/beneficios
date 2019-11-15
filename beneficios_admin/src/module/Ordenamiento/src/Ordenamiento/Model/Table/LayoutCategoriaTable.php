<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 01/10/15
 * Time: 05:27 PM
 */
namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\LayoutCategoria;
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
        $resultSet = $this->tableGateway->select("Eliminado = '0'");
        return $resultSet;
    }

    public function fetchAllIndex($id)
    {
        $resultSet = $this->tableGateway->select(array('BNF_Categoria_id' => $id));
        return $resultSet;
    }

    public function getLayoutCategoria($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getLayoutCategoriabyName($nombre, $id = "")
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

    public function getLayoutCategoriaDetails($id = null, $index = null, $empresa_id = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutCategoria');
        if ($empresa_id > 0) {
            $select->where->equalTo('Index', $index)
                ->and->equalTo('BNF_Categoria_id', $id)
                ->and->equalTo('BNF_Empresa_id', $empresa_id);
        } else {
            $select->where->equalTo('Index', $index)
                ->and->equalTo('BNF_Categoria_id', $id)
                ->and->isNull('BNF_Empresa_id');
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveLayoutCategoria(LayoutCategoria $ordenamiento)
    {
        $data = $ordenamiento->getArrayCopy();
        $data['Eliminado'] = '0';
        $id = (int)$ordenamiento->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getLayoutCategoria($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('LayoutCategoria id does not exist');
            }
        }
        return $id;
    }

    public function deleteLayoutCategoria($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getReport()
    {
        $select = new Select();
        $select->from('BNF_Layout');
        $resultSet = $this->tableGateway->select($select);
        return $resultSet;
    }

    public function getLayoutCategoriaExist($id, $empresa_id)
    {
        $select = new Select();
        $select->from('BNF_LayoutCategoria');
        if ($empresa_id > 0) {
            $select->where->equalTo('Eliminado', '0')
                ->and->equalTo('BNF_Categoria_id', $id)
                ->and->equalTo('BNF_Empresa_id', $empresa_id);
        } else {
            $select->where->equalTo('Eliminado', '0')
                ->and->equalTo('BNF_Categoria_id', $id)
                ->and->isNull('BNF_Empresa_id');
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getLayoutCategoriaId($id = null, $index = null,$layout_id = 0, $empresa_id = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutCategoria');
        $select->columns(array('id'));
        if ($empresa_id > 0) {
            $select->where->equalTo('Index', $index)
                ->and->equalTo('BNF_Layout_id', $layout_id)
                ->and->equalTo('BNF_Categoria_id', $id)
                ->and->equalTo('BNF_Empresa_id', $empresa_id);
        } else {
            $select->where->equalTo('Index', $index)
                ->and->equalTo('BNF_Layout_id', $layout_id)
                ->and->equalTo('BNF_Categoria_id', $id)
                ->and->isNull('BNF_Empresa_id');
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}
