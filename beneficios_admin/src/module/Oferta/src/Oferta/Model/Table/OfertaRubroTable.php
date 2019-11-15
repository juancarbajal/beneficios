<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/09/15
 * Time: 10:12 AM
 */

namespace Oferta\Model\Table;

use Oferta\Model\OfertaRubro;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaRubroTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getOfertaRubro($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getOfertaRubroSeach($idOferta, $idRubro)
    {
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id = ' . $idOferta, 'BNF_Rubro_id = ' . $idRubro));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Rubro");
        }
        return $row;
    }

    public function getOfertaRubroExist($idOferta, $idRubro)
    {
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        $select = new Select();
        $select->from('BNF_OfertaRubro');
        $select->where('BNF_Oferta_id = ' . $idOferta . ' AND BNF_Rubro_id = ' . $idRubro);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaRubros($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select("BNF_Oferta_id = " . $id . " AND Eliminado = '0'");
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Rubro $id");
        }
        return $row;
    }

    public function getOfertaRubrosName($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaRubro');
        $select->join(
            'BNF_Rubro',
            'BNF_OfertaRubro.BNF_Rubro_id =  BNF_Rubro.id',
            array('Nombre','Nombre')
        );
        $select->where->equalTo("BNF_Oferta_id", $id);
        $select->where->equalTo("BNF_OfertaRubro.Eliminado", '0');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaRubro(OfertaRubro $ofertaRubro)
    {
        $data = array(
            'BNF_Rubro_id' => $ofertaRubro->BNF_Rubro_id,
            'BNF_Oferta_id' => $ofertaRubro->BNF_Oferta_id,
            'Eliminado' => $ofertaRubro->Eliminado,
        );
        $id = (int)$ofertaRubro->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaRubro($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Oferta Rubro no existe');
            }
        }
        return $id;
    }

    public function deleteRubro($idOferta, $idRubro)
    {
        $data['Eliminado'] = '1';
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        if ($this->getOfertaRubroSeach($idOferta, $idRubro)) {
            $this->tableGateway->update($data, array('BNF_Oferta_id' => $idOferta, 'BNF_Rubro_id' => $idRubro));
        } else {
            throw new \Exception('La Relacion Oferta Rubro no existe');
        }
    }
}
