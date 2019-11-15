<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/09/15
 * Time: 10:21 AM
 */

namespace Oferta\Model\Table;

use Oferta\Model\OfertaSegmento;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaSegmentoTable
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

    public function getOfertaSegmento($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Segmento $id");
        }
        return $row;
    }

    public function getOfertaSegmentoSeach($idOferta, $idSegmento)
    {
        $idOferta = (int)$idOferta;
        $idSegmento = (int)$idSegmento;
        $rowset = $this->tableGateway->select(
            array('BNF_Oferta_id = ' . $idOferta, 'BNF_Segmento_id = ' . $idSegmento)
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Segmento");
        }
        return $row;
    }

    public function getOfertaSegmentoExist($idOferta, $idSegmento)
    {
        $idOferta = (int)$idOferta;
        $idSegmento = (int)$idSegmento;
        $select = new Select();
        $select->from('BNF_OfertaSegmento');
        $select->where('BNF_Oferta_id = ' . $idOferta . ' AND BNF_Segmento_id = ' . $idSegmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaSegmentos($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaSegmento');
        $select->where("BNF_Oferta_id = " . $id . " AND Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertaSegmentosName($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaSegmento');
        $select->join(
            'BNF_Segmento',
            'BNF_OfertaSegmento.BNF_Segmento_id = BNF_Segmento.id',
            array('Nombre' => 'Nombre')
        );
        $select->where->equalTo("BNF_Oferta_id", $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaSegmento(OfertaSegmento $ofertaSegmento)
    {
        $data = array(
            'BNF_Segmento_id' => $ofertaSegmento->BNF_Segmento_id,
            'BNF_Oferta_id' => $ofertaSegmento->BNF_Oferta_id,
            'Eliminado' => $ofertaSegmento->Eliminado,
        );
        $id = (int)$ofertaSegmento->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaSegmento($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Oferta Segmento no existe');
            }
        }
        return $id;
    }

    public function deleteAllSegmentos($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $id));
    }
}
