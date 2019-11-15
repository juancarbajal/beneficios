<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/10/15
 * Time: 05:10 PM
 */

namespace Auth\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmpresaTable
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

    public function getEmpresasClienteEspExist($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array(),
            'left'
        );
        $select->join('BNF_TipoEmpresa', 'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id', array());
        $select->where(
            'BNF_TipoEmpresa.Nombre = "Cliente" AND BNF_Empresa.id = ' . $id . ' AND ClaseEmpresaCliente = "Especial"'
        );
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getEmpresasCliente()
    {
        $resultSet = $this->tableGateway->select('ClaseEmpresaCliente IS NOT NULL');
        $resultSet->buffer();
        return $resultSet;
    }

    public function getEmpresa($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select('id = '. $id);
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getEmpresaSlug($slug)
    {
        $rowset = $this->tableGateway->select('Slug = "'. $slug .'"');
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getEmpresaSubDominio($subDominio)
    {
        $rowset = $this->tableGateway->select('SubDominio = "'. $subDominio .'"');
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}
