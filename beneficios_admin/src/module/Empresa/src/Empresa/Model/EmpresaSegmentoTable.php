<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:52 PM
 */

namespace Empresa\Model;

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

    public function getEmpresaSegmento($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getEmpresaSegmentoIfExist($empresa, $segmento)
    {
        $rowset = $this->tableGateway->select(array('BNF_Empresa_id' => $empresa, 'BNF_Segmento_id' => $segmento));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function getEmpresaSegmentoDatos($empresa, $segmento)
    {
        $id1 = (int)$empresa;
        $id2 = (int)$segmento;
        $rowset = $this->tableGateway->select(array('BNF_Empresa_id' => $id1, 'BNF_Segmento_id' => $id2));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row");
        }
        return $row;
    }

    public function saveEmpresaSegmento(EmpresaSegmento $empresaSegmento)
    {
        $data = array(
            'BNF_Empresa_id' => $empresaSegmento->BNF_Empresa_id,
            'BNF_Segmento_id' => $empresaSegmento->BNF_Segmento_id,
            'Eliminado' => $empresaSegmento->Eliminado
        );

        $id = (int)$empresaSegmento->id;

        if ($id == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getEmpresaSegmento($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('EmpresaSegmento id does not exist');
            }
        }
        return 0;
    }

    public function deleteEmpresaSegmento($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function getSegmentosEmpresa($id)
    {
        $value = (int)$id;
        $select = new Select();
        $select->from('BNF_EmpresaSegmento');
        $select->columns(array("*"));
        $select->join(
            'BNF_Segmento',
            'BNF_Segmento.id = BNF_EmpresaSegmento.BNF_Segmento_id',
            array('NombreSegmento' => 'Nombre')
        );
        $select->where('BNF_EmpresaSegmento.BNF_Empresa_id = ' . $value . ' AND BNF_EmpresaSegmento.Eliminado = 0');

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
