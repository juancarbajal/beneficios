<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 07:03 PM
 */

namespace Campania\Model\Table;

use Campania\Model\Campania;
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

    public function getCampaniaDetail($pais = null, $nombre = null, $order_by = null, $order = null)
    {
        $select = new Select();
        $select->from('BNF_Campanias');
        $select->columns(array('*'));
        $select->join('BNF_CampaniaUbigeo', 'BNF_CampaniaUbigeo.BNF_Campanias_id=BNF_Campanias.id', array());
        $select->join('BNF_Pais', 'BNF_Pais.id=BNF_CampaniaUbigeo.BNF_Pais_id', array('NombrePais' => 'NombrePais'));
        if ($pais != 0 and $nombre == null) {
            $select->where->equalTo("BNF_Pais.id", $pais);
        } elseif ($pais == 0 and $nombre != null) {
            $select->where->like("BNF_Campanias.Nombre", $nombre."%");
        } elseif ($pais != 0 and $nombre != null) {
            $select->where->equalTo('BNF_Pais.id', $pais)
                ->or->like('BNF_Campanias.Nombre', $nombre."%");
        }
        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF_Campanias.id $order");
        }
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getCampania($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCampaniabyName($nombre, $id = "")
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

    public function getCampaniaPais($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Campanias');
        $select->columns(array('id', 'Nombre'));
        $select->join('BNF_CampaniaUbigeo', 'BNF_CampaniaUbigeo.BNF_Campanias_id=BNF_Campanias.id', array());
        $select->where->equalTo('BNF_CampaniaUbigeo.BNF_Pais_id', $id);
        $select->where("BNF_Campanias.Eliminado = '0' AND BNF_CampaniaUbigeo.Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCampaniaEdit($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Campanias');
        $select->columns(array('*'));
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF_CampaniaUbigeo.BNF_Campanias_id=BNF_Campanias.id',
            array('CU_id' => 'id')
        );
        $select->join('BNF_Pais', 'BNF_Pais.id=BNF_CampaniaUbigeo.BNF_Pais_id', array('NombrePais' => 'id'));
        $select->where->equalTo('BNF_Campanias.id', $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveCampania(Campania $campania)
    {
        $data = $campania->getArrayCopy();
        unset($data['NombrePais']);
        unset($data['CU_id']);
        $id = (int)$campania->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getCampania($id)) {
                unset($data['FechaCreacion']);
                unset($data['Eliminado']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El id de la Campaña no existe.');
            }
        }
        return $id;
    }

    public function deleteCampania($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }
}
