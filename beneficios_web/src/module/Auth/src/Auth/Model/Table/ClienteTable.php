<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/10/15
 * Time: 07:21 PM
 */

namespace Auth\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class ClienteTable
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

    public function getCliente($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row) {
            false;
        }
        return $row;
    }

    public function getDocumento($documento)
    {
        $select = new Select();
        $select->from('BNF_Cliente');
        $select->join(
            'BNF_EmpresaClienteCliente',
            'BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF_EmpresaClienteCliente.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );
        $select->where
            ->equalTo('BNF_Cliente.NumeroDocumento', $documento)
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Estado', 'Activo')
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Eliminado', 0);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function verifyCliente($documento, $empresa)
    {
        $select = new Select();
        $select->from('BNF_Cliente');
        $select->join(
            'BNF_EmpresaClienteCliente',
            'BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF_EmpresaClienteCliente.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );
        $select->where
            ->equalTo('BNF_Cliente.NumeroDocumento', $documento)
            ->and
            ->equalTo('BNF_Empresa.id', $empresa)
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Estado', 'Activo')
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Eliminado', 0)
            ->and
            ->equalTo('BNF_Empresa.Cliente', 1);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }


    public function getTipoDocumento($documento)
    {
        $select = new Select();
        $select->from('BNF_Cliente');
        $select->join(
            'BNF_TipoDocumento',
            'BNF_Cliente.BNF_TipoDocumento_id = BNF_TipoDocumento.id',
            array('TipoDocumento' => 'Nombre')
        );
        $select->where->equalTo('BNF_Cliente.NumeroDocumento', $documento);

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        return $row;
    }

    public function getTotalSubgrupo($documento, $empresa)
    {
        $select = new Select();
        $select->from('BNF_Cliente');
        $select->join(
            'BNF_EmpresaSubgrupoCliente',
            'BNF_EmpresaSubgrupoCliente.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF_Subgrupo',
            'BNF_Subgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF_Subgrupo.BNF_Empresa_id',
            array()
        );
        $select->where
            ->equalTo('BNF_Cliente.NumeroDocumento', $documento)
            ->and
            ->equalTo('BNF_Empresa.id', $empresa)
            ->and
            ->equalTo('BNF_EmpresaSubgrupoCliente.Eliminado', '0')
            ->and
            ->equalTo('BNF_Subgrupo.Eliminado', 0);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getClienteNumeroDocumeto($doc)
    {
        $rowset = $this->tableGateway->select(array('NumeroDocumento' => $doc));
        $row = $rowset->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function updateConection($data, $id)
    {
        $this->tableGateway->update($data, array('id' => $id));
    }
}
