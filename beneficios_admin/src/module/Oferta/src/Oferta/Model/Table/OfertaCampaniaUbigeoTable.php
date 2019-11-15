<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/09/15
 * Time: 11:11 AM
 */

namespace Oferta\Model\Table;

use Oferta\Model\OfertaCampaniaUbigeo;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaCampaniaUbigeoTable
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

    public function getOfertaCampaniaUbigeo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Campaña Ubigeo $id");
        }
        return $row;
    }

    public function getOfertaCampaniaUbigeoXCampania($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaCampaniaUbigeo');
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF_OfertaCampaniaUbigeo.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
            array()
        );
        $select->join('BNF_Campanias', 'BNF_Campanias.id = BNF_CampaniaUbigeo.BNF_Campanias_id', array());
        $select->where("BNF_Campanias.id = " . $id);

        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->count();
    }

    public function getOfertaCampaniaUbigeoSeach($idOferta, $idCampaniaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCampaniaUbigeo = (int)$idCampaniaUbigeo;
        $rowset = $this->tableGateway->select(
            array(
                'BNF_Oferta_id = ' . $idOferta,
                'BNF_CampaniaUbigeo_id = ' . $idCampaniaUbigeo
            )
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Campaña Ubigeo");
        }
        return $row;
    }

    public function getOfertaCampaniaUbigeoExist($idOferta, $idCampaniaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCampaniaUbigeo = (int)$idCampaniaUbigeo;
        $select = new Select();
        $select->from('BNF_OfertaCampaniaUbigeo');
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF_OfertaCampaniaUbigeo.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
            array()
        );
        $select->where
            ->equalTo("BNF_Oferta_id", $idOferta)
            ->and
            ->equalTo("BNF_CampaniaUbigeo.BNF_Campanias_id", $idCampaniaUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaCampaniaUbigeos($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaCampaniaUbigeo');
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF_OfertaCampaniaUbigeo.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
            array(
                'Campania' => 'BNF_Campanias_id', 'Pais' => 'BNF_Pais_id'
            )
        );
        $select->join(
            'BNF_Campanias',
            'BNF_Campanias.id = BNF_CampaniaUbigeo.BNF_Campanias_id',
            array(
                'Nombre' => 'Nombre'
            )
        );
        $select->where("BNF_Oferta_id = " . $id . " AND BNF_OfertaCampaniaUbigeo.Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaCampaniaUbigeo(OfertaCampaniaUbigeo $ofertaCampaniaUbigeo)
    {
        $data = array(
            'BNF_Oferta_id' => $ofertaCampaniaUbigeo->BNF_Oferta_id,
            'BNF_CampaniaUbigeo_id' => $ofertaCampaniaUbigeo->BNF_CampaniaUbigeo_id,
            'Eliminado' => $ofertaCampaniaUbigeo->Eliminado,
        );
        $id = (int)$ofertaCampaniaUbigeo->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaCampaniaUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Oferta Campaña Ubigeo no existe');
            }
        }
        return $id;
    }

    public function deleteAllCampaniaUbigeo($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $id));
    }
}
