<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/09/15
 * Time: 05:45 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\Imagen;
use Zend\Db\TableGateway\TableGateway;

class ImagenTable
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

    public function getImagenOferta($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('BNF_Oferta_id' => $id));
        return $resultSet;
    }

    public function getImagen($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Imagen $id");
        }
        return $row;
    }

    public function saveImagen(Imagen $imagen)
    {
        $data = array(
            'BNF_Oferta_id' => $imagen->BNF_Oferta_id,
            'Nombre' => $imagen->Nombre,
            'Principal' => $imagen->Principal,
            'Eliminado' => '0'
        );
        $id = (int)$imagen->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getImagen($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Imagen no existe');
            }
        }
        return $id;
    }

    public function deleteImagen($id)
    {
        $nombre = $this->getImagen($id);
        $this->tableGateway->delete(array('id' => (int)$id));

        return $nombre->Nombre;
    }
    public function principalImagen($id, $oferta_id)
    {
        $this->noprincipalImagen($oferta_id);
        $data['Principal'] = '1';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));

        return $id;
    }

    public function noprincipalImagen($id)
    {
        $data['Principal'] = '0';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF_Oferta_id' => (int)$id));

        return $id;
    }
}
