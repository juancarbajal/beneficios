<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:11 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosCampania;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosCampaniaTable
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

    public function getAllOfertaPuntosCampania($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF2_Oferta_Puntos_id" => $id));
        return $resultSet;
    }

    public function getOfertaPuntosCampania($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPuntosCampaniaUbigeoSearch($idOferta, $idCampaniaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCampaniaUbigeo = (int)$idCampaniaUbigeo;
        $rowset = $this->tableGateway->select(
            array(
                'BNF2_Oferta_Puntos_id' => $idOferta,
                'BNF_CampaniaUbigeo_id' => $idCampaniaUbigeo
            )
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Campaña Ubigeo");
        }
        return $row;
    }

    public function getIfExist($idOferta, $idCampaniaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCampaniaUbigeo = (int)$idCampaniaUbigeo;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Campania');
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF2_Oferta_Puntos_Campania.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
            array()
        );
        $select->where
            ->equalTo("BNF2_Oferta_Puntos_id", $idOferta)
            ->and
            ->equalTo("BNF_CampaniaUbigeo.BNF_Campanias_id", $idCampaniaUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaCampaniaUbigeos($id)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Campania');
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF2_Oferta_Puntos_Campania.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
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
        $select->where("BNF2_Oferta_Puntos_id = " . $id . " AND BNF2_Oferta_Puntos_Campania.Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaPuntosCampania(OfertaPuntosCampania $ofertaPuntosCampania)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $ofertaPuntosCampania->BNF2_Oferta_Puntos_id,
            'BNF_CampaniaUbigeo_id' => $ofertaPuntosCampania->BNF_CampaniaUbigeo_id,
            'Eliminado' => $ofertaPuntosCampania->Eliminado,
        );

        $id = (int)$ofertaPuntosCampania->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosCampania($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosCampania id does not exist');
            }
        }
    }

    public function deleteAllOfertaPuntosCampania($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $id));
    }
}
