<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/09/15
 * Time: 10:31 AM
 */

namespace Oferta\Model\Table;

use Oferta\Model\OfertaCategoriaUbigeo;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaCategoriaUbigeoTable
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

    public function getOfertaCategoriaUbigeo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Categoria Ubigeo $id");
        }
        return $row;
    }

    public function getOfertaCategoriaUbigeoSeach($idOferta, $idCategoriaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCategoriaUbigeo = (int)$idCategoriaUbigeo;
        $rowset = $this->tableGateway->select(
            array(
                'BNF_Oferta_id = ' . $idOferta,
                'BNF_CategoriaUbigeo_id = ' . $idCategoriaUbigeo
            )
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Categoria Ubigeo");
        }
        return $row;
    }

    public function getOfertaCategoriaUbigeoExist($idOferta, $idCategoriaUbigeo)
    {
        $idOferta = (int)$idOferta;
        $idCategoriaUbigeo = (int)$idCategoriaUbigeo;
        $select = new Select();
        $select->from('BNF_OfertaCategoriaUbigeo');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF_OfertaCategoriaUbigeo.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array()
        );
        $select->where
            ->equalTo("BNF_Oferta_id", $idOferta)
            ->and
            ->equalTo("BNF_CategoriaUbigeo.BNF_Categoria_id", $idCategoriaUbigeo);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaCategoriaUbigeoXCategoria($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaCategoriaUbigeo');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF_OfertaCategoriaUbigeo.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array()
        );
        $select->join('BNF_Categoria', 'BNF_Categoria.id = BNF_CategoriaUbigeo.BNF_Categoria_id', array());
        $select->where("BNF_Categoria.id = " . $id);

        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->count();
    }

    public function getOfertaCategoriaUbigeos($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaCategoriaUbigeo');
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF_OfertaCategoriaUbigeo.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array('Categoria' => 'BNF_Categoria_id', 'Pais' => 'BNF_Pais_id')
        );
        $select->join(
            'BNF_Categoria',
            'BNF_Categoria.id = BNF_CategoriaUbigeo.BNF_Categoria_id',
            array('Nombre' => 'Nombre')
        );
        $select->where("BNF_Oferta_id = " . $id . " AND BNF_OfertaCategoriaUbigeo.Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaCategoriaUbigeo(OfertaCategoriaUbigeo $ofertaCategoriaUbigeo)
    {
        $data = array(
            'BNF_Oferta_id' => $ofertaCategoriaUbigeo->BNF_Oferta_id,
            'BNF_CategoriaUbigeo_id' => $ofertaCategoriaUbigeo->BNF_CategoriaUbigeo_id,
            'Eliminado' => $ofertaCategoriaUbigeo->Eliminado,
        );
        $id = (int)$ofertaCategoriaUbigeo->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaCategoriaUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Oferta Categoria Ubigeo no existe');
            }
        }
        return $id;
    }

    public function deleteAllCategoriaUbigeo($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $id));
    }
}
