<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/10/15
 * Time: 05:36 PM
 */

namespace Application\Model\Table;


use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class BannersCampaniasTable
{
    protected $tableGateway;
    protected $campania;
    protected $empresa;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select("Eliminado = '0'");
        return $resultSet;
    }

    public function getBannerCampaniaAll($campania, $empresa)
    {
        $this->campania = (int)$campania;
        $this->empresa = (int)$empresa;
        $resultSet = $this->tableGateway->select(
            function (Select $select) {
                $select->columns(array('BNF_Banners_id','Imagen','Url'));
                $select->where->equalTo('BNF_Campanias_id', $this->campania)
                    ->and->equalTo('Eliminado', '0');
                if ($this->empresa != 0) {
                    $select->where->equalTo('BNF_Empresa_id', $this->empresa);
                } else {
                    $select->where->isNull('BNF_Empresa_id');
                }
                $select->order('BNF_Banners_id ASC');
            }
        );
        if (!count($resultSet)) {
            $resultSet = $this->tableGateway->select(
                function (Select $select) {
                    $select->columns(array('BNF_Banners_id','Imagen','Url'));
                    $select->where->equalTo('BNF_Campanias_id', $this->campania)
                        ->and->equalTo('Eliminado', '0')->and->isNull('BNF_Empresa_id');
                    $select->order('BNF_Banners_id ASC');

                    /*$query = $select->getSqlString();
                    echo str_replace('"', '', $query);
                    exit;*/
                }
            );
        }
        return $resultSet;
    }

    public function getBannerCampania($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getBannerCampaniabyBanner($banner, $campania)
    {
        $rowset = $this->tableGateway->select(array("BNF_Banners_id" => $banner, "BNF_Campanias_id" => $campania));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getBannerCampaniaExist($banner, $campania)
    {
        $rowset = $this->tableGateway->select(array("BNF_Banners_id" => $banner, "BNF_Campanias_id" => $campania));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }
}
