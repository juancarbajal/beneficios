<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/10/15
 * Time: 04:18 PM
 */

namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\BannersCategoria;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class BannersCategoriaTable
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

    public function getBannerCategoriaAll()
    {
        $resultSet = $this->tableGateway->select(null);
        return $resultSet;
    }

    public function getBannerCategoria($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getBannersbyCategoria($categoria, $empresa)
    {
        $select = new Select();
        $select->from('BNF_BannersCategoria');
        $select->columns(array('id', 'Imagen', 'Url', 'Eliminado', 'BNF_Banners_id'));
        $select->join(
            'BNF_Banners',
            'BNF_BannersCategoria.BNF_Banners_id = BNF_Banners.id',
            array('NombreBanner' => 'Nombre')
        );
        $select->where->equalTo('BNF_Categoria_id', $categoria);
        if ((int)$empresa != 0) {
            $select->where->equalTo('BNF_Empresa_id', $empresa);
        } else {
            $select->where->isNull('BNF_Empresa_id');
        }
        $select->order('BNF_Banners_id');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $rowSet = $this->tableGateway->selectWith($select);
        return $rowSet->toArray();
    }

    public function getBannerCategoriabyBanner($banner, $categoria, $empresa)
    {
        if ((int)$empresa == 0) {
            $rowSet = $this->tableGateway->select(
                array('BNF_Banners_id' => $banner, 'BNF_Categoria_id' => $categoria, "BNF_Empresa_id IS NULL")
            );
        } else {
            $rowSet = $this->tableGateway->select(
                array('BNF_Banners_id' => $banner, 'BNF_Categoria_id' => $categoria, 'BNF_Empresa_id' => $empresa)
            );
        }

        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getBannerCategoriaExist($banner, $categoria, $empresa)
    {
        $empresa = (int)$empresa;
        if ($empresa == 0) {
            $rowSet = $this->tableGateway->select(
                array('BNF_Banners_id' => $banner, 'BNF_Categoria_id' => $categoria, "BNF_Empresa_id IS NULL")
            );
        } else {
            $rowSet = $this->tableGateway->select(
                array('BNF_Banners_id' => $banner, 'BNF_Categoria_id' => $categoria, 'BNF_Empresa_id' => $empresa)
            );
        }
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function saveBannerCategoria(BannersCategoria $banner)
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
            if ($this->getBannerCategoria($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                return $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Banner id does not exist');
            }
        }
    }

    public function editLinkBannerCategoria($id, $val)
    {
        $data['Url'] = $val;
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function deleteBannerCategoria($id, $val, $ban)
    {
        $data['Eliminado'] = $val;
        return $this->tableGateway->update($data, array('id' => $id, 'BNF_Categoria_id' => $ban));
    }
}
