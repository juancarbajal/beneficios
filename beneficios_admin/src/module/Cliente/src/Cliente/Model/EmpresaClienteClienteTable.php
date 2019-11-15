<?php

namespace Cliente\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmpresaClienteClienteTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getEmpresaCliente($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function searchEmpresaCliente($empresa, $cliente)
    {
        $rowset = $this->tableGateway->select(array('BNF_Empresa_id' => $empresa, 'BNF_Cliente_id' => $cliente));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function searchEmpresaClientebyDoc($empresa, $doc)
    {
        $select = new Select();
        $select->from("BNF_EmpresaClienteCliente");
        $select->join(
            "BNF_Cliente",
            "BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id",
            array()
        );

        $select->where->equalTo("BNF_Empresa_id", $empresa)
            ->and->equalTo('NumeroDocumento', $doc);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        if (!$resultSet) {
            return false;
        }
        return $resultSet->current();
    }

    public function searchEmpresaClienteActive($empresa, $doc)
    {
        $select = new Select();
        $select->from("BNF_EmpresaClienteCliente");
        $select->join(
            "BNF_Cliente",
            "BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id",
            array()
        );

        $select->where->equalTo("BNF_Empresa_id", $empresa)
            ->and->equalTo('NumeroDocumento', $doc)
            ->and->equalTo('BNF_EmpresaClienteCliente.Estado', 'Activo')
            ->and->equalTo('BNF_EmpresaClienteCliente.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        if (!$resultSet) {
            return false;
        }
        return $resultSet->current();
    }

    public function saveEmpresaCliente(EmpresaClienteCliente $empresaClienteCliente)
    {
        $data = array(
            'BNF_Empresa_id' => $empresaClienteCliente->BNF_Empresa_id,
            'BNF_Cliente_id' => $empresaClienteCliente->BNF_Cliente_id,
            'Estado' => $empresaClienteCliente->Estado,
            'Eliminado' => 0,
        );

        $id = (int)$empresaClienteCliente->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresaCliente($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Cliente Empresa id does not exist');
            }
        }
    }

    public function deleteCliente($cliente)
    {
        $this->tableGateway->update(array('Eliminado' => 1), array('BNF_Cliente_id' => $cliente));
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

    public function updateArray($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }

    public function delete($where)
    {
        $this->tableGateway->delete($where);
    }

    public function updateByEmpresaAndClient($data, $empresa, $cliente)
    {
        $this->tableGateway->update($data, array('BNF_Empresa_id' => $empresa, 'BNF_Cliente_id' => $cliente));
    }

    public function getClientesXEmpresa($empresa, $fechaInicio, $fechaFin)
    {
        $select = new Select();
        $select->from("BNF_EmpresaClienteCliente");
        $select->join('BNF_Cliente', 'BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id', array());
        if ($empresa != '') {
            $select->where->equalTo("BNF_Empresa_id", $empresa);
        }
        $select->where
            ->equalTo('Estado', 'Activo');
        $select->where(
            "BNF_Cliente.FechaCreacion BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY)"
        );
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        if (!$resultSet) {
            return false;
        }
        return count($resultSet);
    }

    public function searchByDoc($doc)
    {
        $select = new Select();
        $select->from("BNF_EmpresaClienteCliente");
        $select->join(
            "BNF_Cliente",
            "BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id"
        );
        $select->where->equalTo('NumeroDocumento', $doc);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function searchByClientId($idcliente)
    {
        $select = new Select();
        $select->from("BNF_EmpresaClienteCliente");
        $select->where->equalTo('BNF_Cliente_id', $idcliente);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }
}
