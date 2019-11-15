<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:21 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosRubro;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosRubroTable
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

    public function getOfertaPuntosRubroByIdOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id, "Eliminado" => 0));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPuntosRubro($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPuntosRubroSearch($idOferta, $idRubro)
    {
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        $rowset = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $idOferta, 'BNF_Rubro_id' => $idRubro));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Rubro");
        }
        return $row;
    }

    public function getIfExist($idOferta, $idRubro)
    {
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Rubro');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $idOferta)
            ->and->equalTo('BNF_Rubro_id', $idRubro);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveOfertaPuntosRubro(OfertaPuntosRubro $ofertaPuntosRubro)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $ofertaPuntosRubro->BNF2_Oferta_Puntos_id,
            'BNF_Rubro_id' => $ofertaPuntosRubro->BNF_Rubro_id,
            'Eliminado' => $ofertaPuntosRubro->Eliminado,
        );

        $id = (int)$ofertaPuntosRubro->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosRubro($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosRubro id does not exist');
            }
        }
    }

    public function deleteOfertaPuntosRubro($idOferta, $idRubro)
    {
        $data['Eliminado'] = '1';
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        if ($this->getOfertaPuntosRubroSearch($idOferta, $idRubro)) {
            $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $idOferta, 'BNF_Rubro_id' => $idRubro));
        } else {
            throw new \Exception('La Relacion Oferta Rubro no existe');
        }
    }
}
