<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/08/16
 * Time: 05:56 PM
 */

namespace Application\Model\Table;

use Application\Model\CuponPuntosLog;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponPuntosLogTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllCuponPuntos()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getCuponPuntosLog($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCuponPuntosLogByCuponId($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF2_Cupon_Puntos_id' => $id, 'EstadoCupon' => 'Redimido'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCuponPuntosLogByOfertaPuntos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCuponPuntosLogByEstado($id, $estado)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos_Log');
        $select->where->equalTo('BNF2_Cupon_Puntos_id', $id);
        $select->where->equalTo('EstadoCupon', $estado);
        $select->order("FechaCreacion DESC");

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function saveCuponPuntosLog(CuponPuntosLog $cuponPuntosLog)
    {
        $data = array(
            'BNF2_Cupon_Puntos_id' => $cuponPuntosLog->BNF2_Cupon_Puntos_id,
            'CodigoCupon' => $cuponPuntosLog->CodigoCupon,
            'EstadoCupon' => $cuponPuntosLog->EstadoCupon,
            'BNF2_Oferta_Puntos_id' => $cuponPuntosLog->BNF2_Oferta_Puntos_id,
            'BNF2_Oferta_Puntos_Atributos_id' => $cuponPuntosLog->BNF2_Oferta_Puntos_Atributos_id,
            'BNF_Cliente_id' => $cuponPuntosLog->BNF_Cliente_id,
            'BNF_Usuario_id' => $cuponPuntosLog->BNF_Usuario_id,
            'Comentario' => $cuponPuntosLog->Comentario,
        );

        $id = (int)$cuponPuntosLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('CuponPuntosLog id no create');
        }
    }
}
