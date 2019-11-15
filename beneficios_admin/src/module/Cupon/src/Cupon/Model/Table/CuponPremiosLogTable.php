<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/08/16
 * Time: 05:56 PM
 */

namespace Cupon\Model\Table;

use Cupon\Model\CuponPremiosLog;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponPremiosLogTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllCuponPremios()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getCuponPremiosLog($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCuponPremiosLogByCuponId($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF3_Cupon_Premios_id' => $id, 'EstadoCupon' => 'Redimido'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCuponPremiosLogByOfertaPremios($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCuponPremiosLogByEstado($id, $estado)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios_Log');
        $select->where->equalTo('BNF3_Cupon_Premios_id', $id);
        $select->where->equalTo('EstadoCupon', $estado);
        $select->order("FechaCreacion DESC");

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function saveCuponPremiosLog(CuponPremiosLog $cuponPremiosLog)
    {
        $data = array(
            'BNF3_Cupon_Premios_id' => $cuponPremiosLog->BNF3_Cupon_Premios_id,
            'CodigoCupon' => $cuponPremiosLog->CodigoCupon,
            'EstadoCupon' => $cuponPremiosLog->EstadoCupon,
            'BNF3_Oferta_Premios_id' => $cuponPremiosLog->BNF3_Oferta_Premios_id,
            'BNF3_Oferta_Premios_Atributos_id' => $cuponPremiosLog->BNF3_Oferta_Premios_Atributos_id,
            'BNF_Cliente_id' => $cuponPremiosLog->BNF_Cliente_id,
            'BNF_Usuario_id' => $cuponPremiosLog->BNF_Usuario_id,
            'Comentario' => $cuponPremiosLog->Comentario,
        );

        $id = (int)$cuponPremiosLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('CuponPremiosLog id no create');
        }
    }

    public function getCuponPremiosLogByCupon($id)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios_Log');
        $select->where->equalTo('BNF3_Cupon_Premios_id', $id);
        $select->order("FechaCreacion ASC");

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }
}
