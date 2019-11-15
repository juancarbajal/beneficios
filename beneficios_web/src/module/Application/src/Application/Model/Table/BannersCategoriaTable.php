<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/10/15
 * Time: 05:37 PM
 */

namespace Application\Model\Table;


use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class BannersCategoriaTable
{
    protected $tableGateway;
    protected $categoria;
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

    public function getBannerCategoriaAll($categoria, $empresa)
    {
        $this->categoria = (int)$categoria;
        $this->empresa = (int)$empresa;
        $resultSet = $this->tableGateway->select(
            function (Select $select) {
                $select->columns(array('BNF_Banners_id','Imagen','Url'));
                $select->where->equalTo('BNF_Categoria_id', $this->categoria)
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
                    $select->where->equalTo('BNF_Categoria_id', $this->categoria)
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

    public function getBannerCategoria($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getBannerCategoriabyBanner($banner, $categoria)
    {
        $rowset = $this->tableGateway->select(array('BNF_Banners_id' => $banner, 'BNF_Categoria_id' => $categoria));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getBannerCategoriaExist($banner, $categoria)
    {
        $rowset = $this->tableGateway->select(array('BNF_Banners_id' => $banner, 'BNF_Categoria_id' => $categoria));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function getBannerOferta($categoria, $empresa)
    {
        $this->categoria = (int)$categoria;
        $this->empresa = (int)$empresa;
        $resultSet = $this->tableGateway->select(
            function (Select $select) {
                $select->columns(array('BNF_Banners_id','Imagen','Url'));
                $select->where->equalTo('BNF_Categoria_id', $this->categoria)
                    ->and->equalTo('Eliminado', '0')
                    ->and->equalTo('BNF_Banners_id', 6);
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
                    $select->where->equalTo('BNF_Categoria_id', $this->categoria)
                        ->and->equalTo('Eliminado', '0')
                        ->and->isNull('BNF_Empresa_id')
                        ->and->equalTo('BNF_Banners_id', 6);
                    $select->order('BNF_Banners_id ASC');

                    /*$query = $select->getSqlString();
                    echo str_replace('"', '', $query);
                    exit;*/
                }
            );
        }
        return $resultSet;
    }
}
