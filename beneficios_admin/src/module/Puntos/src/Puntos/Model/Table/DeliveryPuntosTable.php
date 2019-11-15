<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/08/16
 * Time: 11:36 AM
 */

namespace Puntos\Model\Table;

use Puntos\Model\DeliveryPuntos;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class DeliveryPuntosTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getFormulario($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id));
        return $rowset;
    }

    public function getExistFormulario($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function fetchAllDeliveryPuntos()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getDeliveryPuntos($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getIfNameExist($oferta_id, $nombre)
    {
        $select = new Select();
        $select->from('BNF2_Delivery_Puntos');
        $select->where->like('Nombre_Campo', $nombre)
            ->AND->equalTo('BNF2_Oferta_Puntos_id', $oferta_id);
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveDeliveryPuntos(DeliveryPuntos $deliveryPuntos)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $deliveryPuntos->BNF2_Oferta_Puntos_id,
            'Etiqueta_Campo' => $deliveryPuntos->Etiqueta_Campo,
            'Nombre_Campo' => $deliveryPuntos->Nombre_Campo,
            'Tipo_Campo' => $deliveryPuntos->Tipo_Campo,
            'Detalle' => $deliveryPuntos->Detalle,
            'Requerido' => $deliveryPuntos->Requerido,
            'Activo' => $deliveryPuntos->Activo,
        );

        $id = (int)$deliveryPuntos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getDeliveryPuntos($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('DeliveryPuntos id does not exist');
            }
        }
    }

    public function deleteFormulario($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
