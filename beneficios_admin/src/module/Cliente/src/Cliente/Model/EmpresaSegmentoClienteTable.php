<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 02/09/15
 * Time: 07:18 PM
 */

namespace Cliente\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmpresaSegmentoClienteTable
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

    public function getEmpresaSegmentoCliente($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('idBNF_EmpresaSegmentoCliente' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not fin row $id");
        }
        return $row;
    }

    public function getEmpresaSegmentoClienteData($empresaSegmento, $cliente)
    {
        $rowset = $this->tableGateway->select(
            array(
                'BNF_EmpresaSegmento_id' => $empresaSegmento,
                'BNF_Cliente_id' => $cliente
            )
        );
        $row = $rowset->current();
        return (!$row) ? false : $row;
    }

    public function getEmpresaSegmentoClienteIfExist($cliente)
    {
        $rowset = $this->tableGateway->select(
            array(
                'BNF_Cliente_id' => $cliente
            )
        );
        $row = $rowset;
        return $row;
    }

    public function getEmpresaSegmentoClienteDataExist($empresaSegmento, $cliente)
    {
        $rowset = $this->tableGateway->select(
            array(
                'BNF_EmpresaSegmento_id' => $empresaSegmento,
                'BNF_Cliente_id' => $cliente
            )
        );
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function saveEmpresaSegmentoCliente(EmpresaSegmentoCliente $empresaSegmentoCliente)
    {
        $data = array(
            'BNF_EmpresaSegmento_id' => $empresaSegmentoCliente->BNF_EmpresaSegmento_id,
            'BNF_Cliente_id' => $empresaSegmentoCliente->BNF_Cliente_id,
            'Eliminado' => $empresaSegmentoCliente->Eliminado,
        );

        $id = (int)$empresaSegmentoCliente->idBNF_EmpresaSegmentoCliente;

        if ($id == 0) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresaSegmentoCliente($id)) {
                return $this->tableGateway->update($data, array('idBNF_EmpresaSegmentoCliente' => $id));
            } else {
                throw new \Exception('La Relacion de Empresa Segmento Cliente no existe.');
            }
        }
    }

    public function delete($where)
    {
        $this->tableGateway->delete($where);
    }

    public function updateArray($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }

    public function getEmpresaSegmentoClienteCurrent($empresa, $cliente)
    {

        $select = new Select();
        $select->from('BNF_EmpresaSegmentoCliente');
        $select->join(
            'BNF_EmpresaSegmento',
            'BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id =  BNF_EmpresaSegmento.id',
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

    public function getEmpresaSegmentoClienteByEmpresa($empresa, $cliente)
    {

        $select = new Select();
        $select->from('BNF_EmpresaSegmentoCliente');
        $select->join(
            'BNF_EmpresaSegmento',
            'BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id =  BNF_EmpresaSegmento.id',
            array()
        );

        $select->where->equalTo('BNF_Empresa_id', $empresa)->and->equalTo('BNF_Cliente_id', $cliente);

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function searchByDoc($doc)
    {
        $select = new Select();
        $select->from("BNF_EmpresaSegmentoCliente");
        $select->join(
            "BNF_Cliente",
            "BNF_Cliente.id = BNF_EmpresaSegmentoCliente.BNF_Cliente_id"
        );
        $select->where->equalTo('NumeroDocumento', $doc);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function searchByClientId($idcliente)
    {
        $select = new Select();
        $select->from("BNF_EmpresaSegmentoCliente");
        $select->where->equalTo('BNF_Cliente_id', $idcliente)
            ->and->equalTo("Eliminado", 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }
}
