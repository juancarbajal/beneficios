<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/09/15
 * Time: 07:06 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\OfertaUbigeo;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaUbigeoTable
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

    public function getOfertaUbigeo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Ubigeo $id");
        }
        return $row;
    }

    public function getOfertaUbigeoSeach($idOferta, $idUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idUbigeo = (int)$idUbigeo;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id = ' . $idOferta, 'BNF_Ubigeo_id = ' . $idUbigeo));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Segmento");
        }
        return $row;
    }

    public function getOfertaUbigeoExist($idOferta, $idUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idUbigeo = (int)$idUbigeo;
        $select = new Select();
        $select->from('BNF_OfertaUbigeo');
        $select->where('BNF_Oferta_id = ' . $idOferta . ' AND BNF_Ubigeo_id = ' . $idUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaUbigeos($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaUbigeo');
        $select->where("BNF_Oferta_id = " . $id . " AND Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertaUbigeosPais($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaUbigeo');
        $select->join(
            'BNF_Ubigeo',
            'BNF_OfertaUbigeo.BNF_Ubigeo_id = BNF_Ubigeo.id',
            array()
        );
        $select->join(
            'BNF_Pais',
            'BNF_Ubigeo.BNF_Pais_id = BNF_Pais.id',
            array('NombrePais' => 'NombrePais')
        );
        $select->where->equalTo("BNF_Oferta_id", $id)
            ->and->equalTo('BNF_OfertaUbigeo.Eliminado', '0');
        $select->group("NombrePais");
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function saveOfertaUbigeo(OfertaUbigeo $ofertaUbigeo)
    {
        $data = array(
            'BNF_Ubigeo_id' => $ofertaUbigeo->BNF_Ubigeo_id,
            'BNF_Oferta_id' => $ofertaUbigeo->BNF_Oferta_id,
            'Eliminado' => $ofertaUbigeo->Eliminado,
        );
        $id = (int)$ofertaUbigeo->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Oferta Ubigeo no existe');
            }
        }
        return $id;
    }

    public function deleteAllUbigeos($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $id));
    }
}
