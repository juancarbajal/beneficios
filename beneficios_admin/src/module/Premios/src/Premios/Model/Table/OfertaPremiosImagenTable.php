<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 03:18 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremiosImagen;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosImagenTable
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

    public function getAllOfertaPremiosImagen($id)
    {
        $resultSet = $this->tableGateway->select(array("Eliminado" => 0, "BNF3_Oferta_Premios_id" => $id));
        return $resultSet;
    }

    public function getAllImagesOfertaPremios($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id));
        return $resultSet;
    }
    
    public function getOfertaPremiosImagen($id)
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

    public function saveOfertaPremiosImagen(OfertaPremiosImagen $OfertaPremiosImagen)
    {
        $data = array(
            'BNF3_Oferta_Premios_id' => $OfertaPremiosImagen->BNF3_Oferta_Premios_id,
            'Nombre' => $OfertaPremiosImagen->Nombre,
            'Principal' => $OfertaPremiosImagen->Principal,
            'Eliminado' => $OfertaPremiosImagen->Eliminado,
        );

        $id = (int)$OfertaPremiosImagen->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremiosImagen($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremiosImagen id does not exist');
            }
        }
    }

    public function deleteOfertaPremiosImagen($id)
    {
        $nombre = $this->getOfertaPremiosImagen($id);
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
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => (int)$id));
        return $id;
    }
}
