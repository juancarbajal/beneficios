<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/10/15
 * Time: 07:23 PM
 */

namespace Auth\Model\Table;

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

    public function getTotalEmpresasCliente($documento)
    {
        $select = new Select();
        $select->from('BNF_EmpresaClienteCliente');
        $select->join(
            'BNF_Cliente',
            'BNF_EmpresaClienteCliente.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF_EmpresaClienteCliente.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array()
        );

        $select->where
            ->equalTo('BNF_Cliente.NumeroDocumento', $documento)
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Estado', 'Activo')
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Eliminado', 0)
            ->and
            ->equalTo('BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id', 2)
            ->and
            ->equalTo('BNF_EmpresaTipoEmpresa.Eliminado', 0);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getEmpresasClientebyDoc($documento)
    {
        $select = new Select();
        $select->from('BNF_EmpresaClienteCliente');
        $select->join(
            'BNF_Cliente',
            'BNF_EmpresaClienteCliente.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF_EmpresaClienteCliente.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array()
        );

        $select->where
            ->equalTo('BNF_Cliente.NumeroDocumento', $documento)
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Estado', 'Activo')
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Eliminado', 0)
            ->and
            ->equalTo('BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id', 2)
            ->and
            ->equalTo('BNF_EmpresaTipoEmpresa.Eliminado', 0);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function verifybyidClienteandidEmpresa($documento, $idEmpresa){
        $select = new Select();
        $select->from('BNF_EmpresaClienteCliente');
        $select->join(
            'BNF_Cliente',
            'BNF_EmpresaClienteCliente.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->where->equalTo('BNF_EmpresaClienteCliente.BNF_Empresa_id', (int) $idEmpresa)
            ->and->equalTo('BNF_Cliente.NumeroDocumento', $documento);
        $resultSet = $this->tableGateway->selectWith($select);
        return ($resultSet->count()) ? true :false;
    }
}
