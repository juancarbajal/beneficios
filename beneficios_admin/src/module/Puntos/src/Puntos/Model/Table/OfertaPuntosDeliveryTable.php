<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/08/16
 * Time: 11:36 AM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosDelivery;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosDeliveryTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getExistOfertaPuntosDelivery($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF2_Delivery_Puntos_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function fetchAllOfertaPuntosDelivery()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getOfertaPuntosDelivery($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveOfertaPuntosDelivery(OfertaPuntosDelivery $ofertaPuntosDelivery)
    {
        $data = array(
            'BNF2_Delivery_Puntos_id' => $ofertaPuntosDelivery->BNF2_Delivery_Puntos_id,
            'BNF2_Oferta_Puntos_id' => $ofertaPuntosDelivery->BNF2_Oferta_Puntos_id,
            'BNF2_Asignacion_Puntos_id' => $ofertaPuntosDelivery->BNF2_Asignacion_Puntos_id,
            'BNF_Empresa_id' => $ofertaPuntosDelivery->BNF_Empresa_id,
            'BNF_Cliente_id' => $ofertaPuntosDelivery->BNF_Cliente_id,
            'Detalle' => $ofertaPuntosDelivery->Detalle,
        );

        $id = (int)$ofertaPuntosDelivery->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosDelivery($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosDelivery id does not exist');
            }
        }
    }
}
