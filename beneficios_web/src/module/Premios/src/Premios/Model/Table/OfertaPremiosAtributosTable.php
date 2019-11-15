<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:06 PM
 */

namespace Premios\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosAtributosTable
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

    public function getAllOfertaPremiosAtributos($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF3_Oferta_Premios_id" => $id));
        return $resultSet;
    }

    public function getOfertaPremiosAtributos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPremiosAtributosSearch($idOferta, $nombreAtributo)
    {
        $idOferta = (int)$idOferta;
        $rowset = $this->tableGateway->select(
            array('BNF3_Oferta_Premios_id' => $idOferta, 'NombreAtributo' => $nombreAtributo)
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
        $select->from('BNF3_Oferta_Premios_Atributos');
        $select->where
            ->equalTo("BNF3_Oferta_Premios_id", $idOferta)
            ->and
            ->equalTo("NombreAtributo", $nombreAtributo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function updateOfertaPremiosAtributos($data, $idOferta, $idAtributo)
    {
        return $this->tableGateway->update($data, array("id" => $idAtributo, 'BNF3_Oferta_Premios_id' => $idOferta));
    }

    public function getTotalHabilitados($id)
    {
        $resultSet = $this->tableGateway->select(array("Stock > 0" , "BNF3_Oferta_Premios_id" => $id));
        return $resultSet->count();
    }
}
