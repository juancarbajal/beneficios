<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/10/16
 * Time: 08:35 PM
 */

namespace Application\Model\Table;

use Application\Model\OfertaAtributos;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaAtributosTable
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

    public function getIfExist($idOferta, $nombreAtributo)
    {
        $idOferta = (int)$idOferta;
        $select = new Select();
        $select->from('BNF_Oferta_Atributos');
        $select->where
            ->equalTo("BNF_Oferta_id", $idOferta)
            ->and
            ->equalTo("NombreAtributo", $nombreAtributo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaAtributosSearch($idOferta, $nombreAtributo)
    {
        $idOferta = (int)$idOferta;
        $rowset = $this->tableGateway->select(
            array('BNF_Oferta_id' => $idOferta, 'NombreAtributo' => $nombreAtributo)
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Atributo");
        }
        return $row;
    }


    public function getAllOfertaAtributos($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF_Oferta_id" => $id));
        return $resultSet;
    }

    public function getOfertaAtributos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Oferta Atributo $id");
        }
        return $row;
    }

    public function saveOfertaAtributos(OfertaAtributos $ofertaAtributos)
    {
        $data = array(
            'BNF_Oferta_id' => $ofertaAtributos->BNF_Oferta_id,
            'NombreAtributo' => $ofertaAtributos->NombreAtributo,
            'Stock' => (int)$ofertaAtributos->Stock,
            'StockInicial' => (int)$ofertaAtributos->StockInicial,
            'FechaVigencia' => $ofertaAtributos->FechaVigencia,
            'Eliminado' => $ofertaAtributos->Eliminado,
        );

        $id = (int)$ofertaAtributos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaAtributos($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Oferta Atributo no existe');
            }
        }
        return $id;
    }

    public function updateOfertaAtributos($data, $idOferta, $idAtributo)
    {
        return $this->tableGateway->update($data, array("id" => $idAtributo, 'BNF_Oferta_id' => $idOferta));
    }

    public function getTotalHabilitados($id)
    {
        $resultSet = $this->tableGateway->select(array("Stock > 0" , "BNF_Oferta_id" => $id, 'Eliminado' => 0));
        return $resultSet->count();
    }
}
