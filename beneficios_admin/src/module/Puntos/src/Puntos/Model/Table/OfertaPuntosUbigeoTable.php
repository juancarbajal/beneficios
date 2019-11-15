<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:23 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosUbigeo;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosUbigeoTable
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

    public function getAllOfertaPuntosUbigeo($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF2_Oferta_Puntos_id" => $id));
        return $resultSet;
    }

    public function getOfertaPuntosUbigeo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaUbigeoSearch($idOferta, $idUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idUbigeo = (int)$idUbigeo;
        $rowset = $this->tableGateway->select(
            array('BNF2_Oferta_Puntos_id' => $idOferta, 'BNF_Ubigeo_id' => $idUbigeo)
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Segmento");
        }
        return $row;
    }

    public function getIfExist($idOferta, $idUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idUbigeo = (int)$idUbigeo;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Ubigeo');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $idOferta)
            ->and->equalTo('BNF_Ubigeo_id', $idUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveOfertaPuntosUbigeo(OfertaPuntosUbigeo $ofertaPuntosUbigeo)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $ofertaPuntosUbigeo->BNF2_Oferta_Puntos_id,
            'BNF_Ubigeo_id' => $ofertaPuntosUbigeo->BNF_Ubigeo_id,
            'Eliminado' => $ofertaPuntosUbigeo->Eliminado,
        );

        $id = (int)$ofertaPuntosUbigeo->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosUbigeo id does not exist');
            }
        }
    }

    public function deleteAllOfertaPuntosUbigeo($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $id));
    }
}
