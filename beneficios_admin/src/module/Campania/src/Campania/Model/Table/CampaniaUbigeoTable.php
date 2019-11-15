<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 11/09/15
 * Time: 12:01 AM
 */

namespace Campania\Model\Table;

use Campania\Model\CampaniaUbigeo;
use Zend\Db\TableGateway\TableGateway;

class CampaniaUbigeoTable
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

    public function getCampaniaUbigeo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Campaña-Ubigeo $id");
        }
        return $row;
    }

    public function getCampaniaUbigeoPais($camp, $pais)
    {
        $id = (int)$camp;
        $pais = (int)$pais;
        $rowset = $this->tableGateway->select(
            array("BNF_Campanias_id = " . $id,
                "BNF_Pais_id = " . $pais,
                "Eliminado = '0'"
            )
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Campaña-Ubigeo $id Pais $pais");
        }
        return $row;
    }

    public function getCampaniaUbigeobyCamp($camp)
    {
        $id = (int)$camp;
        $rowset = $this->tableGateway->select(array("BNF_Campanias_id = " . $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Campaña-Ubigeo $id.");
        }
        return $row;
    }

    public function saveCampaniaUbigeo(CampaniaUbigeo $campaniaUbigeo)
    {
        $data = $campaniaUbigeo->getArrayCopy();
        $id = (int)$campaniaUbigeo->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getCampaniaUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Campaña Ubigeo no existe');
            }
        }
        return $id;
    }

    public function deleteCampaniaUbigeo($id, $val)
    {
        $data['Eliminado'] = $val;
        $this->tableGateway->update($data, array('id' => (int)$id));
    }
}
