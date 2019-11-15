<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:11 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremiosCampania;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosCampaniaTable
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

    public function getAllOfertaPremiosCampania($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF3_Oferta_Premios_id" => $id));
        return $resultSet;
    }

    public function getOfertaPremiosCampania($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPremiosCampaniaUbigeoSearch($idOferta, $idCampaniaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCampaniaUbigeo = (int)$idCampaniaUbigeo;
        $rowset = $this->tableGateway->select(
            array(
                'BNF3_Oferta_Premios_id' => $idOferta,
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
        $select->from('BNF3_Oferta_Premios_Campania');
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF3_Oferta_Premios_Campania.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
            array()
        );
        $select->where
            ->equalTo("BNF3_Oferta_Premios_id", $idOferta)
            ->and
            ->equalTo("BNF_CampaniaUbigeo.BNF_Campanias_id", $idCampaniaUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaCampaniaUbigeos($id)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios_Campania');
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF3_Oferta_Premios_Campania.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
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
        $select->where("BNF3_Oferta_Premios_id = " . $id . " AND BNF3_Oferta_Premios_Campania.Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaPremiosCampania(OfertaPremiosCampania $OfertaPremiosCampania)
    {
        $data = array(
            'BNF3_Oferta_Premios_id' => $OfertaPremiosCampania->BNF3_Oferta_Premios_id,
            'BNF_CampaniaUbigeo_id' => $OfertaPremiosCampania->BNF_CampaniaUbigeo_id,
            'Eliminado' => $OfertaPremiosCampania->Eliminado,
        );

        $id = (int)$OfertaPremiosCampania->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremiosCampania($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremiosCampania id does not exist');
            }
        }
    }

    public function deleteAllOfertaPremiosCampania($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $id));
    }
}
