<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:16 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosCategoria;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosCategoriaTable
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

    public function getAllOfertaPuntosCategoria($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF2_Oferta_Puntos_id" => $id));
        return $resultSet;
    }

    public function getOfertaPuntosCategoria($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPuntosCategoriaUbigeoSearch($idOferta, $idCategoriaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCategoriaUbigeo = (int)$idCategoriaUbigeo;
        $rowset = $this->tableGateway->select(
            array(
                'BNF2_Oferta_Puntos_id' => $idOferta,
                'BNF_CategoriaUbigeo_id' => $idCategoriaUbigeo
            )
        );
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getIfExist($idOferta, $idCategoriaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCategoriaUbigeo = (int)$idCategoriaUbigeo;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Categoria');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF2_Oferta_Puntos_Categoria.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array()
        );
        $select->where
            ->equalTo("BNF2_Oferta_Puntos_id", $idOferta)
            ->and
            ->equalTo("BNF_CategoriaUbigeo.BNF_Categoria_id", $idCategoriaUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaPuntosCategoriaUbigeos($id)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos_Categoria');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF2_Oferta_Puntos_Categoria.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array('Categoria' => 'BNF_Categoria_id', 'Pais' => 'BNF_Pais_id')
        );
        $select->join(
            'BNF_Categoria',
            'BNF_Categoria.id = BNF_CategoriaUbigeo.BNF_Categoria_id',
            array('Nombre' => 'Nombre')
        );
        $select->where("BNF2_Oferta_Puntos_id = " . $id . " AND BNF2_Oferta_Puntos_Categoria.Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaPuntosCategoria(OfertaPuntosCategoria $ofertaPuntosCategoria)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $ofertaPuntosCategoria->BNF2_Oferta_Puntos_id,
            'BNF_CategoriaUbigeo_id' => $ofertaPuntosCategoria->BNF_CategoriaUbigeo_id,
            'Eliminado' => $ofertaPuntosCategoria->Eliminado,
        );

        $id = (int)$ofertaPuntosCategoria->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosCategoria($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosCategoria id does not exist');
            }
        }
    }

    public function deleteAllOfertaPuntosCategoria($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $id));
    }
}
