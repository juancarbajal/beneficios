<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:31 PM
 */

namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\LayoutPuntos;
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
        $resultSet = $this->tableGateway->select("Eliminado = '0'");
        return $resultSet;
    }

    public function getLayoutPuntos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getLayoutPuntosByName($nombre, $id = "")
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

    public function getLayoutPuntosDetails($index = null, $empresa_id = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutPuntos');
        if ($empresa_id > 0) {
            $select->where->equalTo('Index', $index)
                ->and->equalTo('BNF_Empresa_id', $empresa_id);
        } else {
            $select->where->equalTo('Index', $index)
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

    public function saveLayoutPuntos(LayoutPuntos $ordenamiento)
    {
        $data = $ordenamiento->getArrayCopy();
        $data['Eliminado'] = '0';
        $id = (int)$ordenamiento->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getLayoutPuntos($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('LayoutPuntos id dont exist');
            }
        }
        return $id;
    }

    public function deleteLayoutPuntos($id, $val)
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

    public function getLayoutPuntosExist($empresa_id)
    {
        $select = new Select();
        $select->from('BNF_LayoutPuntos');
        if ($empresa_id > 0) {
            $select->where->equalTo('Eliminado', '0')
                ->and->equalTo('BNF_Empresa_id', $empresa_id);
        } else {
            $select->where->equalTo('Eliminado', '0')
                ->and->isNull('BNF_Empresa_id');
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getLayoutPuntosId($index = null,$layout_id = 0, $empresa_id = 0)
    {
        $select = new Select();
        $select->from('BNF_LayoutPuntos');
        $select->columns(array('id'));
        if ($empresa_id > 0) {
            $select->where->equalTo('Index', $index)
                ->and->equalTo('BNF_Layout_id', $layout_id)
                ->and->equalTo('BNF_Empresa_id', $empresa_id);
        } else {
            $select->where->equalTo('Index', $index)
                ->and->equalTo('BNF_Layout_id', $layout_id)
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