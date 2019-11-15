<?php

namespace Usuario\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class SubGrupoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select('Eliminado != 1');
        return $resultSet;
    }

    public function getSubGrupo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            false;
        }
        return $row;
    }

    public function getSubgrupoEmpresa($id)
    {
        $value = (int)$id;
        $resultSet = $this->tableGateway->select('BNF_Empresa_id = ' . $value . ' AND Eliminado = 0');
        if (!$resultSet) {
            false;
        }
        return $resultSet;
    }

    public function getRelSubgrupoEmpresa($id, $sub)
    {
        $value = (int)$id;
        $sub = (int)$sub;
        $resultSet = $this->tableGateway->select(
            'id = ' . $sub . ' AND BNF_Empresa_id = ' . $value . ' AND Eliminado = 0'
        );
        if (!$resultSet) {
            throw new \Exception("Could not find row $id");
        }
        return $resultSet;
    }

    public function getSubgruposEmpresa($id)
    {
        $value = (int)$id;
        $select = new Select();
        $select->from('BNF_Subgrupo');
        $select->where('BNF_Empresa_id = ' . $value . ' AND Eliminado = 0');

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveSubGrupo(SubGrupo $subGrupo)
    {
        $data = array(
            'Nombre' => $subGrupo->Nombre,
            'BNF_Empresa_id' => $subGrupo->BNF_Empresa_id,
            'Eliminado' => 0
        );

        $id = (int)$subGrupo->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSubGrupo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Subgrupo id does not exist');
            }
        }
    }

    public function deleteAllSubgrupoEmpresa($idempresa)
    {
        $data = array(
            'Eliminado' => 1
        );

        $id = (int)$idempresa;

        $this->tableGateway->update($data, array('BNF_Empresa_id' => $id));
    }


    public function deleteSubGrupo($id, $val)
    {
        $data['Eliminado'] = $val;
        $this->tableGateway->update($data, array('id' => $id));
    }

    public function getSubGrupoByName($nombre, $empresa)
    {
        $rowset = $this->tableGateway->select(array('BNF_Empresa_id' => $empresa, 'Nombre' => $nombre));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getSubGrupoXName($nombre, $empresa)
    {
        $rowset = $this->tableGateway->select(array('BNF_Empresa_id' => $empresa, 'Nombre' => $nombre));
        $row = $rowset->current();

        if (!$row) {
            $row = new SubGrupo();
            $row->id = '0';
            return $row;
        }
        return $row;
    }
}
