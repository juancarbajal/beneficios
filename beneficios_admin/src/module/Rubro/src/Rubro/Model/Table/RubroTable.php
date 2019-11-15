<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 12:23 PM
 */

namespace Rubro\Model\Table;

use Rubro\Model\Rubro;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class RubroTable
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

    public function getRubro($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getRubrobyName($nombre, $id = "")
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

    public function getRubroDetails($nombre = null, $order_by = null, $order = null)
    {
        $select = new Select();
        $select->from('BNF_Rubro');
        $select->columns(array('*'));
        if (isset($nombre)) {
            $select->where("BNF_Rubro.Nombre like '" . $nombre . "%'");
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF_Rubro.id $order");
        }
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function saveRubro(Rubro $rubro)
    {
        $data = $rubro->getArrayCopy();
        $id = (int)$rubro->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRubro($id)) {
                unset($data['FechaCreacion']);
                unset($data['Eliminado']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteRubro($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getReport()
    {
        $select = new Select();
        $select->from('BNF_Rubro');
        $resultSet = $this->tableGateway->select($select);
        return $resultSet;
    }
}
