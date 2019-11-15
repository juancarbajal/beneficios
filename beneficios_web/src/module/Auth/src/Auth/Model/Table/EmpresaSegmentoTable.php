<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/12/15
 * Time: 04:53 PM
 */

namespace Auth\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmpresaSegmentoTable
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

    public function getEmpresasSegmento($empresa, $cliente)
    {
        $select = new Select();
        $select->from('BNF_EmpresaSegmento');
        $select->join(
            'BNF_EmpresaSegmentoCliente',
            'BNF_EmpresaSegmento.id = BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id',
            array()
        );
        $select->where->equalTo('BNF_EmpresaSegmentoCliente.BNF_Cliente_id', $cliente)
            ->and->equalTo('BNF_EmpresaSegmentoCliente.Eliminado', 0)
            ->and->equalTo('BNF_EmpresaSegmento.BNF_Empresa_id', $empresa);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}