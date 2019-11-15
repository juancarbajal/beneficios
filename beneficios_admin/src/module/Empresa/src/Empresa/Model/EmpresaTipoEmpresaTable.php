<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:42 PM
 */

namespace Empresa\Model;


use Zend\Db\TableGateway\TableGateway;

class EmpresaTipoEmpresaTable
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

    public function getEmpresaTipoEmpresaRelations($idEmpresa)
    {
        $id = (int)$idEmpresa;
        $resultSet = $this->tableGateway->select('BNF_Empresa_id =' . $id);
        return $resultSet;
    }

    public function getEmpresaTipoEmpresa($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveEmpresaTipoEmpresa(EmpresaTipoEmpresa $empresaTipoEmpresa)
    {
        $data = array(
            'BNF_TipoEmpresa_id' => $empresaTipoEmpresa->BNF_TipoEmpresa_id,
            'BNF_Empresa_id' => $empresaTipoEmpresa->BNF_Empresa_id,
            'Eliminado' => '0',
        );
        //var_dump($data);exit;
        $id = (int)$empresaTipoEmpresa->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresaTipoEmpresa($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteEmpresaTipoEmpresa($id, $val, $tipo)
    {
        $data['Eliminado'] = $val;
        return $this->tableGateway->update($data, array('BNF_Empresa_id' => (int)$id, 'BNF_TipoEmpresa_id' => (int)$tipo));
    }
}
