<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:34 PM
 */

namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\LayoutPuntosPosicion;
use Zend\Db\TableGateway\TableGateway;

class LayoutPuntosPosicionTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select("Eliminado = '0'");
        return $resultSet;
    }

    public function getLayoutPuntosPosicion($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveLayoutPuntos(LayoutPuntosPosicion $ordenamiento)
    {
        $data = $ordenamiento->getArrayCopy();
        $data['Eliminado'] = '0';
        $id = (int)$ordenamiento->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getLayoutPuntosPosicion($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('LayoutPuntosPosicion id does not exist');
            }
        }
    }

    public function getLayoutPuntosPosicionDetails($id = null, $oferta_id = null)
    {
        $rowset = $this->tableGateway->select(array('BNF_LayoutPuntos_id' => $id, 'BNF2_Oferta_Puntos_id' => $oferta_id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function desactivarOfertas($id = null)
    {
        $data['Eliminado'] = 1;
        return $this->tableGateway->update($data, array('BNF_LayoutPuntos_id' => $id));
    }

    public function getOfertasIds($id_layout)
    {
        $resultSet = $this->tableGateway->select(array('BNF_LayoutPuntos_id' => $id_layout, "Eliminado" => '0'));
        return $resultSet;
    }
}