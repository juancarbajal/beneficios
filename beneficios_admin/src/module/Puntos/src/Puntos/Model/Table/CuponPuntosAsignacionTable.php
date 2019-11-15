<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:14 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\CuponPuntosAsignacion;
use Zend\Db\TableGateway\TableGateway;

class CuponPuntosAsignacionTable
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

    public function get($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveAsignacion(CuponPuntosAsignacion $asignacion)
    {
        $data = array(
            'BNF2_Cupon_Puntos_id' => $asignacion->BNF2_Cupon_Puntos_id,
            'BNF2_Asignacion_Puntos_id' => $asignacion->BNF2_Asignacion_Puntos_id,
            'PunosUtilizados' => $asignacion->PunosUtilizados
        );

        $id = (int)$asignacion->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->get($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Asignacion id does not exist');
            }
        }
        return $id;
    }

    public function actualizarAsignacion($data, $id)
    {
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getByCupon($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF2_Cupon_Puntos_id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}
