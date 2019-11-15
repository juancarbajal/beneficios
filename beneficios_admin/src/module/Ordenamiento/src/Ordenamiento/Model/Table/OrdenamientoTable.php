<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/09/15
 * Time: 04:40 PM
 */
namespace Ordenamiento\Model\Table;

use Ordenamiento\Model\Ordenamiento;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;

class OrdenamientoTable
{
    protected $tableGateway;
    protected $serviceLocator;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select("Eliminado = '0'");
        return $resultSet;
    }

    public function getOrdenamiento($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOrdenamientobyName($nombre, $id = "")
    {
        if ($id == "") {
            $rowset = $this->tableGateway->select(array('Nombre' => $nombre));
        } else {
            $rowset = $this->tableGateway->select(array('Nombre' => $nombre, 'id != ' . $id));
        }
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function getOrdenamientoDetails($nombre = null, $order_by = null, $order = null)
    {
        $sql1="SELECT  `BNF_LayoutCampania`.`id` AS  `id`, `BNF_LayoutCampania`.`Index` AS  `Index` ,
        `BNF_LayoutCampania`.`Eliminado` AS  `Eliminado` , 'Campaña' AS  `Tipo` ,
        `BNF_Layout`.`Nombre` AS `NombreLayout` ,  `BNF_Campanias`.`Nombre` AS  `NombreTipo` ,
        `BNF_LayoutCampania`.`FechaCreacion` AS  `FechaCreacion`
        FROM  `BNF_LayoutCampania`
        INNER JOIN  `BNF_Layout` ON  `BNF_Layout`.`id` =  `BNF_LayoutCampania`.`BNF_Layout_id`
        INNER JOIN  `BNF_Campanias` ON  `BNF_Campanias`.`id` =  `BNF_LayoutCampania`.`BNF_Campanias_id`";

        if (isset($nombre)) {
            $sql1 .= " WHERE `BNF_Layout`.`Nombre` LIKE "."'%" . $nombre . "%'";
        }
        $sql2="SELECT  `BNF_LayoutCategoria`.`id` AS  `id`, `BNF_LayoutCategoria`.`Index` AS  `Index` ,
        `BNF_LayoutCategoria`.`Eliminado` AS  `Eliminado` , 'Categoría' AS  `Tipo` ,  `BNF_Layout`.`Nombre` AS
        `NombreLayout` ,  `BNF_Categoria`.`Nombre` AS  `NombreTipo`,
        `BNF_LayoutCategoria`.`FechaCreacion` AS  `FechaCreacion`
        FROM  `BNF_LayoutCategoria`
        INNER JOIN  `BNF_Layout` ON  `BNF_Layout`.`id` =  `BNF_LayoutCategoria`.`BNF_Layout_id`
        INNER JOIN  `BNF_Categoria` ON  `BNF_Categoria`.`id` =  `BNF_LayoutCategoria`.`BNF_Categoria_id`";

        if (isset($nombre)) {
            $sql2 .= " WHERE `BNF_Layout`.`Nombre` LIKE "."'%" . $nombre . "%'";
        }
        $sql='('.$sql1.') UNION ('.$sql2.')';
        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $sql .= " ORDER BY  `".$order_by."` ".$order;
        } else {
            $sql .= " ORDER BY  `id` ".$order;
        }

        $dbAdapter =  $this->tableGateway->adapter->getDriver();
        $statement = $dbAdapter->createStatement($sql);
        $resultSet = $statement->execute();
        $resultSet->buffer();
        return $resultSet;
    }

    public function saveOrdenamiento(Ordenamiento $ordenamiento)
    {
        $data = $ordenamiento->getArrayCopy();
        $data['Eliminado'] = '0';
        $id = (int)$ordenamiento->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getOrdenamiento($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteOrdenamiento($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getReport()
    {
        $select1 = new Select();
        $select1->from('BNF_LayoutCampania');
        $select1->columns(
            array(
                'id',
                'Index',
                'Eliminado',
                'FechaCreacion',
                'FechaActualizacion',
                "Tipo" => new Expression('"Campaña"')
            )
        );
        $select1->join(
            'BNF_Layout',
            'BNF_Layout.id = BNF_LayoutCampania.BNF_Layout_id',
            array('NombreLayout' => 'Nombre')
        );
        $select1->join(
            'BNF_Campanias',
            'BNF_Campanias.id = BNF_LayoutCampania.BNF_Campanias_id',
            array('NombreTipo' => 'Nombre')
        );
        $select2 = new Select();
        $select2->from('BNF_LayoutCategoria');
        $select2->columns(
            array(
                'id',
                'Index',
                'Eliminado',
                'FechaCreacion',
                'FechaActualizacion',
                "Tipo" => new Expression('"Categoría"')
            )
        );
        $select2->join(
            'BNF_Layout',
            'BNF_Layout.id = BNF_LayoutCategoria.BNF_Layout_id',
            array('NombreLayout' => 'Nombre')
        );
        $select2->join(
            'BNF_Categoria',
            'BNF_Categoria.id = BNF_LayoutCategoria.BNF_Categoria_id',
            array('NombreTipo' => 'Nombre')
        );

        $select1->combine($select2, 'UNION ALL');

        $select1->order("FechaCreacion DESC");
        //echo $select3->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select1);
        $resultSet->buffer();
        return $resultSet;
    }
}
