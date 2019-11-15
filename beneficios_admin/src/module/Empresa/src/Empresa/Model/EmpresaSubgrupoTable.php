<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:56 PM
 */

namespace Empresa\Model;


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
        $select = new \Zend\Db\Sql\Select();
        $select->from('BNF_Subgrupo');
        $select->where('Eliminado != 1');
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
        /*$resultSet = $this->tableGateway->select();
        return $resultSet;*/
    }

    public function getEmpresaSubgrupo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getEmpresaSubgrupoDatos($empresa, $subgrupo)
    {
        $id1 = (int)$empresa;
        $id2 = (int)$subgrupo;
        $rowset = $this->tableGateway->select(array('BNF_Empresa_id' => $id1, 'BNF_Subgrupo_id' => $id2));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row");
        }
        return $row;
    }

    public function saveEmpresaSubgrupo(EmpresaSubgrupo $empresaSegmento)
    {
        $data = array(//
        );

        $id = (int)$empresaSegmento->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresaSubgrupo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteEmpresaSubgrupo($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function getSubgruposByEmpresa($idEmpresa)
    {
        $select = new \Zend\Db\Sql\Select();
        $select->from('BNF_Subgrupo');
        $select->where('BNF_Subgrupo.BNF_Empresa_id', $idEmpresa);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
