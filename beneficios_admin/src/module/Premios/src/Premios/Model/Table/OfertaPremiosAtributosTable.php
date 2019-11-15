<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:06 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremiosAtributos;
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

    public function haveAtributosWhitStock($idOferta)
    {
        $idOferta = (int)$idOferta;
        $select = new Select();
        $select->from('BNF3_Oferta_Premios_Atributos');
        $select->where->equalTo("BNF3_Oferta_Premios_id", $idOferta)
            ->AND->greaterThan("Stock", 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveOfertaPremiosAtributos(OfertaPremiosAtributos $OfertaPremiosAtributos)
    {
        $data = array(
            'BNF3_Oferta_Premios_id' => $OfertaPremiosAtributos->BNF3_Oferta_Premios_id,
            'NombreAtributo' => $OfertaPremiosAtributos->NombreAtributo,
            'PrecioVentaPublico' => $OfertaPremiosAtributos->PrecioVentaPublico,
            'PrecioBeneficio' => $OfertaPremiosAtributos->PrecioBeneficio,
            'Stock' => $OfertaPremiosAtributos->Stock,
            'FechaVigencia' => $OfertaPremiosAtributos->FechaVigencia,
            'Eliminado' => $OfertaPremiosAtributos->Eliminado,
        );

        $id = (int)$OfertaPremiosAtributos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremiosAtributos($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremiosAtributos id does not exist');
            }
        }
        return $id;
    }

    public function deleteOfertaPremiosAtributos($idOferta)
    {
        $data['Eliminado'] = '1';
        $idOferta = (int)$idOferta;
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $idOferta));
    }

    public function caducarOfertaPremiosAtributos($id)
    {
        $data['Stock'] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }
}
