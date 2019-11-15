<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/10/15
 * Time: 10:29 PM
 */

namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\Galeria;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

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

    public function getGaleriaAll($empresa = null)
    {
        $select = new Select();
        $select->from('BNF_Galeria');
        if ((int) $empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }

        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->toArray();
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

    public function saveGaleria(Galeria $galeria)
    {
        $data = $galeria->getArrayCopy();
        $id = (int)$galeria->id;
        if ($id == 0) {
            $data['FechaSubida'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getGaleria($id)) {
                unset($data['FechaSubida']);
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Banner id does not exist');
            }
        }
        return 0;
    }

    public function editlinkGaleria($id, $val)
    {
        $data['Url'] = $val;
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function deleteGaleria($id, $val)
    {
        $data['Eliminado'] = $val;
        return $this->tableGateway->delete(array('id' => (int)$id));
    }
}
