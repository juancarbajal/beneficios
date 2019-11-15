<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:16 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremiosCategoria;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosCategoriaTable
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

    public function getAllOfertaPremiosCategoria($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF3_Oferta_Premios_id" => $id));
        return $resultSet;
    }

    public function getOfertaPremiosCategoria($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPremiosCategoriaUbigeoSearch($idOferta, $idCategoriaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCategoriaUbigeo = (int)$idCategoriaUbigeo;
        $rowset = $this->tableGateway->select(
            array(
                'BNF3_Oferta_Premios_id' => $idOferta,
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
        $select->from('BNF3_Oferta_Premios_Categoria');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF3_Oferta_Premios_Categoria.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array()
        );
        $select->where
            ->equalTo("BNF3_Oferta_Premios_id", $idOferta)
            ->and
            ->equalTo("BNF_CategoriaUbigeo.BNF_Categoria_id", $idCategoriaUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaPremiosCategoriaUbigeos($id)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios_Categoria');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF3_Oferta_Premios_Categoria.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array('Categoria' => 'BNF_Categoria_id', 'Pais' => 'BNF_Pais_id')
        );
        $select->join(
            'BNF_Categoria',
            'BNF_Categoria.id = BNF_CategoriaUbigeo.BNF_Categoria_id',
            array('Nombre' => 'Nombre')
        );
        $select->where("BNF3_Oferta_Premios_id = " . $id . " AND BNF3_Oferta_Premios_Categoria.Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaPremiosCategoria(OfertaPremiosCategoria $OfertaPremiosCategoria)
    {
        $data = array(
            'BNF3_Oferta_Premios_id' => $OfertaPremiosCategoria->BNF3_Oferta_Premios_id,
            'BNF_CategoriaUbigeo_id' => $OfertaPremiosCategoria->BNF_CategoriaUbigeo_id,
            'Eliminado' => $OfertaPremiosCategoria->Eliminado,
        );

        $id = (int)$OfertaPremiosCategoria->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremiosCategoria($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremiosCategoria id does not exist');
            }
        }
    }

    public function deleteAllOfertaPremiosCategoria($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $id));
    }
}
