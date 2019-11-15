<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 02/09/15
 * Time: 07:19 PM
 */

namespace Cliente\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmpresaSubgrupoClienteTable
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

    public function getEmpresaSubgrupoCliente($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idBNF_EmpresaSubgrupoCliente' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not fin row $id");
        }
        return $row;
    }

    public function getEmpresaSubgrupoClienteIfExist($empresa, $cliente)
    {
        $id1 = (int)$empresa;
        $id2 = (int)$cliente;
        $select = new Select();
        $select->from('BNF_EmpresaSubgrupoCliente');
        $select->join(
            'BNF_Subgrupo',
            'BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id = BNF_Subgrupo.id',
            array()
        );
        $select->where("BNF_Empresa_id = $id1");
        $select->where("BNF_Cliente_id = $id2");
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function getEmpresaSubgrupoClienteDataExist($empresa, $cliente)
    {
        $id1 = (int)$empresa;
        $id2 = (int)$cliente;
        $select = new Select();
        $select->from('BNF_EmpresaSubgrupoCliente');
        $select->join(
            'BNF_Subgrupo',
            'BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id = BNF_Subgrupo.id',
            array()
        );
        $select->where("BNF_Empresa_id = $id1");
        $select->where("BNF_Cliente_id = $id2");
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresaSubgrupoClienteData($empresaSubgrupo, $cliente)
    {
        $rowset = $this->tableGateway->select(
            array(
                'BNF_EmpresaSubgrupo_id' => $empresaSubgrupo,
                'BNF_Cliente_id' => $cliente
            )
        );
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not fin row");
        }
        return $row;
    }

    public function saveEmpresaSubgrupoCliente(EmpresaSubgrupoCliente $empresaSubgrupoCliente)
    {
        $data = array(
            'BNF_Subgrupo_id' => $empresaSubgrupoCliente->BNF_Subgrupo_id,
            'BNF_Cliente_id' => $empresaSubgrupoCliente->BNF_Cliente_id,
            'Eliminado' => $empresaSubgrupoCliente->Eliminado,
        );

        $id = $empresaSubgrupoCliente->idBNF_EmpresaSubgrupoCliente;

        if ($id == 0 || !isset($id)) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresaSubgrupoCliente($id)) {
                return $this->tableGateway->update($data, array('idBNF_EmpresaSubgrupoCliente' => $id));
            } else {
                throw new \Exception('Cliente id does not exist');
            }
        }
    }

    public function evalueExist($client, $subgrupo)
    {
        $rowset = $this->tableGateway->select(array('BNF_Cliente_id' => $client, 'BNF_Subgrupo_id' => $subgrupo));
        $row = $rowset->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getSubgruposByCliente($idCliente)
    {
        $select = new Select();
        $select->from('BNF_EmpresaSubgrupoCliente');
        $select->columns(array('*'));
        $select->join(
            'BNF_Subgrupo',
            'BNF_Subgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id',
            array('NombreSubgrupo' => 'Nombre')
        );
        $select->where("BNF_EmpresaSubgrupoCliente.BNF_Cliente_id = $idCliente");
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getSubgruposByClienteAndEmpresa($idCliente, $empresa)
    {
        $select = new Select();
        $select->from('BNF_EmpresaSubgrupoCliente');
        $select->columns(array('*'));
        $select->join(
            'BNF_Subgrupo',
            'BNF_Subgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id',
            array('NombreSubgrupo' => 'id')
        );
        $select->where->equalTo("BNF_EmpresaSubgrupoCliente.BNF_Cliente_id", $idCliente)
            ->and->equalTo("BNF_Subgrupo.BNF_Empresa_id", $empresa)
            ->and->notEqualTo("BNF_Subgrupo.Eliminado", 1);
        $resultSet = $this->tableGateway->selectWith($select);
        //echo $select->getSqlString();exit;
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function insert($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update($data, $id)
    {
        $this->tableGateway->update($data, array('id' => $id));
    }

    public function delete($where)
    {
        $this->tableGateway->delete($where);
    }

    public function updateArray($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }

    public function getEmpresaSubgrupoClienteCurrent($empresa, $cliente)
    {
        $select = new Select();
        $select->from('BNF_EmpresaSubgrupoCliente');
        $select->join(
            'BNF_Subgrupo',
            'BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id = BNF_Subgrupo.id',
            array()
        );

        $select->where->equalTo('BNF_Empresa_id', $empresa)->and->equalTo('BNF_Cliente_id', $cliente);

        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}
