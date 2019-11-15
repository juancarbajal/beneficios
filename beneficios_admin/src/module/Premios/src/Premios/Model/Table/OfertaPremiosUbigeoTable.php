<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:23 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremiosUbigeo;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosUbigeoTable
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

    public function getAllOfertaPremiosUbigeo($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF3_Oferta_Premios_id" => $id));
        return $resultSet;
    }

    public function getOfertaPremiosUbigeo($id)
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
            array('BNF3_Oferta_Premios_id' => $idOferta, 'BNF_Ubigeo_id' => $idUbigeo)
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
        $select->from('BNF3_Oferta_Premios_Ubigeo');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $idOferta)
            ->and->equalTo('BNF_Ubigeo_id', $idUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveOfertaPremiosUbigeo(OfertaPremiosUbigeo $OfertaPremiosUbigeo)
    {
        $data = array(
            'BNF3_Oferta_Premios_id' => $OfertaPremiosUbigeo->BNF3_Oferta_Premios_id,
            'BNF_Ubigeo_id' => $OfertaPremiosUbigeo->BNF_Ubigeo_id,
            'Eliminado' => $OfertaPremiosUbigeo->Eliminado,
        );

        $id = (int)$OfertaPremiosUbigeo->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremiosUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremiosUbigeo id does not exist');
            }
        }
    }

    public function deleteAllOfertaPremiosUbigeo($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $id));
    }
}
