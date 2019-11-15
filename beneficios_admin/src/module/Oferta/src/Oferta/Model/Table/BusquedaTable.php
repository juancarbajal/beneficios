<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/10/15
 * Time: 03:07 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\Busqueda;
use Zend\Db\TableGateway\TableGateway;

class BusquedaTable
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

    public function getBusqueda($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Busqueda $id");
        }
        return $row;
    }

    public function getAllBusquedaEmpresa()
    {
        $resultSet = $this->tableGateway->select(array("Empresa" => 1));
        return $resultSet;
    }

    public function getBusquedaXOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveBusqueda(Busqueda $busqueda)
    {
        $data = array(
            'BNF_Oferta_id' => $busqueda->BNF_Oferta_id,
            'TipoOferta' => (int)$busqueda->TipoOferta,
            'Descripcion' => $busqueda->Descripcion,
            'Empresa' => (int)$busqueda->Empresa
        );
        $id = (int)$busqueda->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getBusqueda($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Busqueda no existe');
            }
        }
        return $id;
    }

    public function updateBusqueda(Busqueda $busqueda)
    {
        $id = null;
        $data = array(
            'BNF_Oferta_id' => $busqueda->BNF_Oferta_id,
            'Descripcion' => $busqueda->Descripcion
        );
        $BNF_Oferta_id = (int)$busqueda->BNF_Oferta_id;
        if ($BNF_Oferta_id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getBusquedaXOferta($BNF_Oferta_id)) {
                $this->tableGateway->update($data, array('BNF_Oferta_id' => $BNF_Oferta_id));
            } else {
                $this->tableGateway->insert($data);
                $id = $this->tableGateway->getLastInsertValue();
            }
        }
        return $id;
    }

    public function deleteImagen($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
