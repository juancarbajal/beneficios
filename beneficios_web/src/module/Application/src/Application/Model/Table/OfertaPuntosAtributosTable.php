<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:06 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosAtributosTable
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

    public function getAllOfertaPuntosAtributos($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF2_Oferta_Puntos_id" => $id));
        return $resultSet;
    }

    public function getOfertaPuntosAtributos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getOfertaPuntosAtributosSearch($idOferta, $nombreAtributo)
    {
        $idOferta = (int)$idOferta;
        $rowset = $this->tableGateway->select(
            array('BNF2_Oferta_Puntos_id' => $idOferta, 'NombreAtributo' => $nombreAtributo)
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Rubro");
        }
        return $row;
    }

    public function getIfExist($idOferta, $nombreAtributo)
    {
        $idOferta = (int)$idOferta;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Atributos');
        $select->where
            ->equalTo("BNF2_Oferta_Puntos_id", $idOferta)
            ->and
            ->equalTo("NombreAtributo", $nombreAtributo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function updateOfertaPuntosAtributos($data, $idOferta, $idAtributo)
    {
        return $this->tableGateway->update($data, array("id" => $idAtributo, 'BNF2_Oferta_Puntos_id' => $idOferta));
    }

    public function getTotalHabilitados($id)
    {
        $resultSet = $this->tableGateway->select(array("Stock > 0", "BNF2_Oferta_Puntos_id" => $id, 'Eliminado' => 0));
        return $resultSet->count();
    }
}
