<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 02:46 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\OfertaCuponCodigo;
use Zend\Db\TableGateway\TableGateway;

class OfertaCuponCodigoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(array('Estado' => '0'));
        return $resultSet;
    }

    public function get($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id, 'Estado' => '0'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getByOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id, 'Estado' => '0'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function save(OfertaCuponCodigo $oferta)
    {
        $data = array(
            'BNF_Oferta_id' => $oferta->BNF_Oferta_id,
            'Codigo' => $oferta->Codigo,
            'EStado' => $oferta->Estado
        );

        $id = (int)$oferta->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOferta($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Oferta no existe');
            }
        }
        return $id;
    }

    public function delete($id)
    {
        $data['Estado'] = '2';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function update($id, $data)
    {
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getExistByCodigo($codigo)
    {
        $rowset = $this->tableGateway->select(array('Codigo' => $codigo, 'Estado' => '2'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCantByOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id, 'Estado' => '0'));
        $row = $rowset->count();
        if (!$row) {
            return 0;
        }
        return $row;
    }

    public function getByCodigo($codigo)
    {
        $rowset = $this->tableGateway->select(array('Codigo' => $codigo));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}
