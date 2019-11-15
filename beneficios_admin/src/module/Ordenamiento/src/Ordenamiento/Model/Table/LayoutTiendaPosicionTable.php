<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 18/04/16
 * Time: 03:14 PM
 */

namespace Ordenamiento\Model\Table;

use Zend\Db\TableGateway\TableGateway;
use Ordenamiento\Model\LayoutTiendaPosicion;

class LayoutTiendaPosicionTable
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

    public function getLayoutTiendaPosicion($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveLayoutTienda(LayoutTiendaPosicion $ordenamiento)
    {
        $data = $ordenamiento->getArrayCopy();
        $data['Eliminado'] = '0';
        $id = (int)$ordenamiento->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getLayoutTiendaPosicion($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('LayoutTiendaPosicion id does not exist');
            }
        }
    }

    public function getLayoutTiendaPosicionDetails($id = null, $oferta_id = null)
    {
        $rowset = $this->tableGateway->select(array('BNF_LayoutTienda_id' => $id, 'BNF_Oferta_id' => $oferta_id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function desactivarOfertas($id = null)
    {
        $data['Eliminado'] = 1;
        return $this->tableGateway->update($data, array('BNF_LayoutTienda_id' => $id));
    }

    public function getOfertasIds($id_layout)
    {
        $resultSet = $this->tableGateway->select(array('BNF_LayoutTienda_id' => $id_layout, "Eliminado" => '0'));
        return $resultSet;
    }
}