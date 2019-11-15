<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:18 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntosImagen;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosImagenTable
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

    public function getAllOfertaPuntosImagen($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF2_Oferta_Puntos_id" => $id));
        return $resultSet;
    }

    public function getAllImagesOfertaPuntos($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id));
        return $resultSet;
    }
    
    public function getOfertaPuntosImagen($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getIfExist($id)
    {
        $id = (int)$id;
        try {
            $rowset = $this->tableGateway->select(array('id' => $id));
            $row = $rowset->current();
            if (!$row) {
                throw new \Exception("Could not find row $id");
            }
            return $row;
        } catch (\Exception $ex) {
            return 0;
        }
    }

    public function saveOfertaPuntosImagen(OfertaPuntosImagen $ofertaPuntosImagen)
    {
        $data = array(
            'BNF2_Oferta_Puntos_id' => $ofertaPuntosImagen->BNF2_Oferta_Puntos_id,
            'Nombre' => $ofertaPuntosImagen->Nombre,
            'Principal' => $ofertaPuntosImagen->Principal,
            'Eliminado' => $ofertaPuntosImagen->Eliminado,
        );

        $id = (int)$ofertaPuntosImagen->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntosImagen($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntosImagen id does not exist');
            }
        }
    }

    public function deleteOfertaPuntosImagen($id)
    {
        $nombre = $this->getOfertaPuntosImagen($id);
        $this->tableGateway->delete(array('id' => (int)$id));

        return $nombre->Nombre;
    }

    public function principalImagen($id, $oferta_id)
    {
        $this->noPrincipalImagen($oferta_id);
        $data['Principal'] = '1';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));

        return $id;
    }

    public function noPrincipalImagen($id)
    {
        $data['Principal'] = '0';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => (int)$id));
        return $id;
    }
}
