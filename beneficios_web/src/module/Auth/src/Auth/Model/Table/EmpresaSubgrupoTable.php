<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/12/15
 * Time: 05:07 PM
 */

namespace Auth\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmpresaSubgrupoTable
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

    public function getEmpresasSubgrupo($empresa, $cliente)
    {
        $cliente = (int)$cliente;
        $select = new Select();
        $select->from('BNF_Subgrupo');
        $select->join(
            'BNF_EmpresaSubgrupoCliente',
            'BNF_Subgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id',
            array()
        );
        $select->where->equalTo('BNF_EmpresaSubgrupoCliente.BNF_Cliente_id', $cliente)
            ->and->equalTo('BNF_Subgrupo.BNF_Empresa_id', $empresa);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}