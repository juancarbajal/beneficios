<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 11/04/16
 * Time: 05:48 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\Tarjetas;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class TarjetasTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(array('Eliminado' => 0));
        return $resultSet;
    }

    public function getTarjetas($id)
    {
        $id = (int)$id;
        $row_set = $this->tableGateway->select(array('id' => $id, 'Eliminado' => 0));
        $row = $row_set->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveTarjetas(Tarjetas $tarjetas)
    {
        $data = array(
            'Descripcion' => $tarjetas->Descripcion,
            'Imagen' => $tarjetas->Imagen,
            'Eliminado' => $tarjetas->Eliminado
        );

        $id = (int)$tarjetas->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getTarjetas($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Tarjeta no existe');
            }
        }
        return $id;
    }

    public function deleteTarjetas($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
