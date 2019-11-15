<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 11/04/16
 * Time: 05:57 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\TarjetasOferta;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class TarjetasOfertaTable
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

    public function getAllTarjetasOferta($idOferta)
    {
        $id = (int)$idOferta;
        $resultSet = $this->tableGateway->select(array('BNF_Oferta_id' => $id, 'Eliminado' => 0));
        return $resultSet;
    }

    public function getTarjetasOfertaData($idOferta, $idTarjeta)
    {
        $row_set = $this->tableGateway->select(array('BNF_Oferta_id' => $idOferta, 'BNF_Tarjetas_id' => $idTarjeta));
        $row = $row_set->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getTarjetasOferta($id)
    {
        $id = (int)$id;
        $row_set = $this->tableGateway->select(array('id' => $id));
        $row = $row_set->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveTarjetasOferta(TarjetasOferta $tarjetasOferta)
    {
        $data = array(
            'BNF_Oferta_id' => $tarjetasOferta->BNF_Oferta_id,
            'BNF_Tarjetas_id' => $tarjetasOferta->BNF_Tarjetas_id,
            'Eliminado' => $tarjetasOferta->Eliminado
        );

        $id = (int)$tarjetasOferta->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getTarjetasOferta($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Tarjeta no existe');
            }
        }
        return $id;
    }

    public function disabledAllTarjetasOferta($id)
    {
        $this->tableGateway->update(
            array('Eliminado' => 1, 'FechaActualizacion' => date("Y-m-d H:i:s")),
            array('BNF_Oferta_id' => (int)$id)
        );
    }
}
