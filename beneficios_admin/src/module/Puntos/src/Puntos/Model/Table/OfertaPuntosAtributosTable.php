<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:06 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosAtributos;
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
            throw new \Exception("Could not find row $id");
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

    public function haveAtributosWhitStock($idOferta)
    {
        $idOferta = (int)$idOferta;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Atributos');
        $select->where->equalTo("BNF2_Oferta_Puntos_id", $idOferta)
            ->AND->greaterThan("Stock", 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveOfertaPuntosAtributos(OfertaPuntosAtributos $ofertaPuntosAtributos)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $ofertaPuntosAtributos->BNF2_Oferta_Puntos_id,
            'NombreAtributo' => $ofertaPuntosAtributos->NombreAtributo,
            'PrecioVentaPublico' => $ofertaPuntosAtributos->PrecioVentaPublico,
            'PrecioBeneficio' => $ofertaPuntosAtributos->PrecioBeneficio,
            'Stock' => $ofertaPuntosAtributos->Stock,
            'FechaVigencia' => $ofertaPuntosAtributos->FechaVigencia,
            'Eliminado' => $ofertaPuntosAtributos->Eliminado,
        );

        $id = (int)$ofertaPuntosAtributos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosAtributos($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosAtributos id does not exist');
            }
        }
        return $id;
    }

    public function deleteOfertaPuntosAtributos($idOferta)
    {
        $data['Eliminado'] = '1';
        $idOferta = (int)$idOferta;
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $idOferta));
    }

    public function caducarOfertaPuntosAtributos($id)
    {
        $data['Stock'] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }
}
