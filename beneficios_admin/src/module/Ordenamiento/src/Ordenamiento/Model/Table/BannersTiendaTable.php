<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/11/15
 * Time: 03:46 PM
 */

namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\BannersTienda;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class BannersTiendaTable
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

    public function getBannerTiendaAll($empresa)
    {
        $select = new Select();
        $select->from('BNF_BannersTienda');
        $select->columns(array('id', 'Imagen', 'Url', 'Eliminado', 'BNF_Banners_id'));
        $select->join(
            'BNF_Banners',
            'BNF_BannersTienda.BNF_Banners_id = BNF_Banners.id',
            array('NombreBanner' => 'Nombre')
        );
        if ((int)$empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $select->order('BNF_Banners_id');
        $rowSet = $this->tableGateway->selectWith($select);
        return $rowSet->toArray();
    }

    public function getBannerTienda($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getBannerTiendabyBanner($banner, $empresa)
    {
        if ((int)$empresa == 0) {
            $rowSet = $this->tableGateway->select(
                array("BNF_Banners_id" => $banner, "BNF_Empresa_id IS NULL")
            );
        } else {
            $rowSet = $this->tableGateway->select(
                array("BNF_Banners_id" => $banner, 'BNF_Empresa_id' => $empresa)
            );
        }

        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getBannerTiendaExist($banner, $empresa)
    {
        $empresa = (int)$empresa;
        if ($empresa == 0) {
            $rowSet = $this->tableGateway->select(
                array("BNF_Banners_id" => $banner, "BNF_Empresa_id IS NULL")
            );
        } else {
            $rowSet = $this->tableGateway->select(
                array("BNF_Banners_id" => $banner, 'BNF_Empresa_id' => $empresa)
            );
        }

        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function saveBannerTienda(BannersTienda $banner)
    {
        $data = $banner->getArrayCopy();
        unset($data['NombreBanner']);
        if ($banner->BNF_Empresa_id == 'all') {
            unset($data['BNF_Empresa_id']);
        }
        $id = (int)$banner->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getBannerTienda($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                return $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Banner id does not exist');
            }
        }
    }

    public function editLinkBannerTienda($id, $val)
    {
        $data['Url'] = $val;
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function deleteBannerTienda($id, $val)
    {
        $data['Eliminado'] = $val;
        return $this->tableGateway->update($data, array('id' => $id));
    }
}
