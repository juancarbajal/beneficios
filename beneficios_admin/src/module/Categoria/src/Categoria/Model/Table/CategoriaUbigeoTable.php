<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 10/09/15
 * Time: 05:50 PM
 */

namespace Categoria\Model\Table;

use Categoria\Model\CategoriaUbigeo;
use Zend\Db\TableGateway\TableGateway;

class CategoriaUbigeoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select("Eliminado = '0'");
        return $resultSet;
    }

    public function getCategoriaUbigeo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Categoria-Ubigeo $id");
        }
        return $row;
    }

    public function getCategoriaUbigeoPais($categoria, $pais)
    {
        $id = (int)$categoria;
        $pais = (int)$pais;
        $rowset = $this->tableGateway->select(
            array(
                'BNF_Categoria_id = ' . $id,
                'BNF_Pais_id = ' . $pais,
                "Eliminado = '0'"
            )
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Categoria-Ubigeo $id Pais $pais");
        }
        return $row;
    }

    public function getCategoriaUbigeoPaisDelete($categoria, $pais)
    {
        $id = (int)$categoria;
        $pais = (int)$pais;
        $rowset = $this->tableGateway->select(
            array(
                'BNF_Categoria_id' => $id,
                'BNF_Pais_id' => $pais,
                "Eliminado" => 1
            )
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Categoria-Ubigeo $id Pais $pais");
        }
        return $row;
    }

    public function getCategoriaUbigeobyCat($cat)
    {
        $id = (int)$cat;
        $rowset = $this->tableGateway->select(array('BNF_Categoria_id = ' . $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Categoria-Ubigeo $id.");
        }
        return $row;
    }

    public function saveCategoriaUbigeo(CategoriaUbigeo $categoriaUbigeo)
    {
        $data = array(
            'BNF_Categoria_id' => $categoriaUbigeo->BNF_Categoria_id,
            'BNF_Pais_id' => $categoriaUbigeo->BNF_Pais_id,
            'Eliminado' => $categoriaUbigeo->Eliminado,
        );
        $id = (int)$categoriaUbigeo->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCategoriaUbigeo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El id de la Relacion Categoria Ubigeo no existe');
            }
        }
    }

    public function deleteCategoriaUbigeo($id, $val)
    {
        $data['Eliminado'] = $val;
        $this->tableGateway->update($data, array('id' => (int)$id));
    }
}
