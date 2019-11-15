<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:21 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosSegmento;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosSegmentoTable
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

    public function getOfertaPuntosSegmentoByOferta($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id, "Eliminado" => 0));
        return $resultSet;
    }

    public function getOfertaPuntosSegmentoByOfertaAll($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id));
        return $resultSet;
    }

    public function getOfertaPuntosSegmento($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPuntosSegmentoSearch($idOferta, $idSegmento)
    {
        $idOferta = (int)$idOferta;
        $idSegmento = (int)$idSegmento;
        $rowSet = $this->tableGateway->select(
            array('BNF2_Oferta_Puntos_id' => $idOferta, 'BNF2_Segmento_id' => $idSegmento)
        );

        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Segmento");
        }
        return $row;
    }

    public function getIfExist($id, $idSegmento)
    {
        $id = (int)$id;
        $idSegmento = (int)$idSegmento;
        $rowset = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id, "BNF2_Segmento_id" => $idSegmento));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveOfertaPuntosSegmento(OfertaPuntosSegmento $OfertaPuntosSegmento)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $OfertaPuntosSegmento->BNF2_Oferta_Puntos_id,
            'BNF2_Segmento_id' => $OfertaPuntosSegmento->BNF2_Segmento_id,
            'Eliminado' => $OfertaPuntosSegmento->Eliminado,
        );

        $id = (int)$OfertaPuntosSegmento->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosSegmento($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosSegmento id does not exist');
            }
        }
    }

    public function deleteAllOfertaPuntosSegmento($id)
    {
        $data['Eliminado'] = '1';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $id));
    }

    public function deleteOfertaPuntosSegmento($id)
    {
        $data['Eliminado'] = '1';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $id = (int)$id;
        $this->tableGateway->update($data, array('id' => $id));
    }
}
