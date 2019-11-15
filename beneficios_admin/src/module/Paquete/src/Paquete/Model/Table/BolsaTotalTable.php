<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 23/09/15
 * Time: 03:22 PM
 */
namespace Paquete\Model\Table;

use Paquete\Model\BolsaTotal;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class BolsaTotalTable
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

    public function getBolsaTotal($idP, $idE)
    {
        $idP = (int)$idP;
        $idE = (int)$idE;
        $rowset = $this->tableGateway->select(array('BNF_TipoPaquete_id = ' . $idP, 'BNF_Empresa_id = ' . $idE));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Bolsa Total");
        }
        return $row;
    }

    public function getBolsaTotalEmpresa($emp)
    {
        $emp = (int)$emp;

        $select = new Select();
        $select->from('BNF_BolsaTotal');
        $select->columns(array('BolsaActual', 'BNF_TipoPaquete_id'));
        $select->join(
            'BNF_TipoPaquete',
            'BNF_BolsaTotal.BNF_TipoPaquete_id = BNF_TipoPaquete.id',
            array('NombreTipoPaquete')
        );
        $select->where("BNF_Empresa_id = " . $emp . " AND BolsaActual > 0");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getBolsa($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Empresa_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveBolsa(BolsaTotal $bolsaTotal)
    {
        $data = $bolsaTotal->getArrayCopy();
        $this->tableGateway->insert($data);
    }

    public function editBolsa(BolsaTotal $bolsaTotal)
    {
        $data = $bolsaTotal->getArrayCopy();
        $BNF_Empresa_id = (int)$bolsaTotal->BNF_Empresa_id;
        $BNF_TipoPaquete_id = (int)$bolsaTotal->BNF_TipoPaquete_id;
        $this->tableGateway->update(
            $data,
            array(
                'BNF_Empresa_id' => $BNF_Empresa_id,
                'BNF_TipoPaquete_id' => $BNF_TipoPaquete_id)
        );
    }

    public function deleteBolsa($id, $val)
    {
        $data['Eliminado'] = $val;
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getBolsaxEmprexTipo($empresa, $tipo)
    {
        $rowset = $this->tableGateway->select(
            array(
                'BNF_Empresa_id' => (int)$empresa,
                'BNF_TipoPaquete_id' => (int)$tipo)
        );
        $row = $rowset->current();
        if (!$row) {
            $bolsa = new BolsaTotal();
            $bolsa->BolsaActual = 0;
            return $bolsa;
        }
        return $row;
    }
}
