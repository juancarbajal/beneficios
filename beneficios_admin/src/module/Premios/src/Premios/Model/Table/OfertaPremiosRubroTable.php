<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:21 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremiosRubro;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosRubroTable
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

    public function getOfertaPremiosRubroByIdOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id, "Eliminado" => 0));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPremiosRubro($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPremiosRubroSearch($idOferta, $idRubro)
    {
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        $rowset = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $idOferta, 'BNF_Rubro_id' => $idRubro));
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
        $select->from('BNF3_Oferta_Premios_Rubro');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $idOferta)
            ->and->equalTo('BNF_Rubro_id', $idRubro);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveOfertaPremiosRubro(OfertaPremiosRubro $OfertaPremiosRubro)
    {
        $data = array(
            'BNF3_Oferta_Premios_id' => $OfertaPremiosRubro->BNF3_Oferta_Premios_id,
            'BNF_Rubro_id' => $OfertaPremiosRubro->BNF_Rubro_id,
            'Eliminado' => $OfertaPremiosRubro->Eliminado,
        );

        $id = (int)$OfertaPremiosRubro->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremiosRubro($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremiosRubro id does not exist');
            }
        }
    }

    public function deleteOfertaPremiosRubro($idOferta, $idRubro)
    {
        $data['Eliminado'] = '1';
        $idOferta = (int)$idOferta;
        $idRubro = (int)$idRubro;
        if ($this->getOfertaPremiosRubroSearch($idOferta, $idRubro)) {
            $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $idOferta, 'BNF_Rubro_id' => $idRubro));
        } else {
            throw new \Exception('La Relacion Oferta Rubro no existe');
        }
    }
}
