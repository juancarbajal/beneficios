<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:21 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremiosSegmento;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosSegmentoTable
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

    public function getOfertaPremiosSegmentoByOferta($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id, "Eliminado" => 0));
        return $resultSet;
    }

    public function getOfertaPremiosSegmentoByOfertaAll($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id));
        return $resultSet;
    }

    public function getOfertaPremiosSegmento($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPremiosSegmentoSearch($idOferta, $idSegmento)
    {
        $idOferta = (int)$idOferta;
        $idSegmento = (int)$idSegmento;
        $rowSet = $this->tableGateway->select(
            array('BNF3_Oferta_Premios_id' => $idOferta, 'BNF3_Segmento_id' => $idSegmento)
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
        $rowset = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id, "BNF3_Segmento_id" => $idSegmento));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveOfertaPremiosSegmento(OfertaPremiosSegmento $OfertaPremiosSegmento)
    {
        $data = array(
            'BNF3_Oferta_Premios_id' => $OfertaPremiosSegmento->BNF3_Oferta_Premios_id,
            'BNF3_Segmento_id' => $OfertaPremiosSegmento->BNF3_Segmento_id,
            'Eliminado' => $OfertaPremiosSegmento->Eliminado,
        );

        $id = (int)$OfertaPremiosSegmento->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremiosSegmento($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremiosSegmento id does not exist');
            }
        }
    }

    public function deleteAllOfertaPremiosSegmento($id)
    {
        $data['Eliminado'] = '1';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $id));
    }

    public function deleteOfertaPremiosSegmento($id)
    {
        $data['Eliminado'] = '1';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $id = (int)$id;
        $this->tableGateway->update($data, array('id' => $id));
    }
}
