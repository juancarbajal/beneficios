<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/10/15
 * Time: 10:11 PM
 */

namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\Banner;
use Zend\Db\TableGateway\TableGateway;

class BannerTable
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

    public function getBannerAll()
    {
        $resultSet = $this->tableGateway->select(null);
        return $resultSet;
    }

    public function getBanner($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveBanner(Banner $banner)
    {
        $data = $banner->getArrayCopy();
        $id = (int)$banner->id;
        if ($id == 0) {
            //$data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBanner($id)) {
                unset($data['FechaCreacion']);
                //$data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Banner id does not exist');
            }
        }
    }

    public function deleteBanner($id, $val)
    {
        $data['Eliminado'] = $val;
        $this->tableGateway->update($data, array('id' => (int)$id));
    }
}
