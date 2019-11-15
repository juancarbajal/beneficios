<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/06/16
 * Time: 05:57 PM
 */

namespace Demanda\Model\Table;

use Demanda\Model\Demanda;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class DemandaTable
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

    public function getDemanda($id)
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

    public function getDetails($order_by, $order, $empresa = "", $fecha = null, $demanda = null)
    {
        $select = new Select();
        $select->from('BNF2_Demanda');
        $select->columns(array('id', 'FechaDemanda'));
        $select->join(
            "BNF_Empresa",
            "BNF2_Demanda.BNF_Empresa_id = BNF_Empresa.id",
            array("Empresa" => 'NombreComercial')
        );
        $select->join(
            "BNF2_Demanda_Segmentos",
            "BNF2_Demanda.id = BNF2_Demanda_Segmentos.BNF2_Demanda_id",
            array()
        );
        $select->join(
            "BNF2_Segmentos",
            "BNF2_Demanda_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id",
            array()
        );
        $select->join(
            "BNF2_Campanias",
            "BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id",
            array("Campania" => 'NombreCampania')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF2_Demanda.BNF_Empresa_id", $empresa);
        }

        if (!empty($demanda)) {
            $select->where->equalTo("BNF2_Campanias.id", $demanda);
        }

        if (!empty($fecha)) {
            $select->where->equalTo("FechaDemanda", $fecha);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF2_Demanda.FechaCreacion DESC");
        }

        $select->where->equalTo("BNF2_Demanda_Segmentos.Eliminado", 0);
        $select->group("BNF2_Demanda.id");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getEmpresasDemandas()
    {
        $select = new Select();
        $select->from('BNF2_Demanda');
        $select->join(
            "BNF_Empresa",
            "BNF2_Demanda.BNF_Empresa_id = BNF_Empresa.id",
            array(
                "id" => "id",
                "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)")
            )
        );
        $select->where->equalTo("BNF2_Demanda.Eliminado", 0);

        //echo $select->getSqlString();
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getCampaniaDemandas()
    {
        $select = new Select();
        $select->from('BNF2_Demanda');
        $select->join(
            "BNF2_Demanda_Segmentos",
            "BNF2_Demanda.id = BNF2_Demanda_Segmentos.BNF2_Demanda_id",
            array()
        );
        $select->join(
            "BNF2_Segmentos",
            "BNF2_Demanda_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id",
            array()
        );
        $select->join(
            "BNF2_Campanias",
            "BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id",
            array("id", "Campania" => 'NombreCampania')
        );

        $select->where->equalTo("BNF2_Demanda_Segmentos.Eliminado", 0);
        $select->where->equalTo("BNF2_Demanda.Eliminado", 0);
        $select->group("NombreCampania");

        //echo $select->getSqlString();
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getReporte($empresa = "", $fecha = null, $demanda = null)
    {
        $select = new Select();
        $select->from('BNF2_Demanda');
        $select->columns(
            array('id', 'FechaDemanda', 'PrecioMinimo', 'PrecioMaximo', 'Target', 'Comentarios', 'Actualizaciones')
        );
        $select->join(
            "BNF_Empresa",
            "BNF2_Demanda.BNF_Empresa_id = BNF_Empresa.id",
            array("Empresa" => 'NombreComercial', 'Ruc', 'CorreoPersonaAtencion')
        );
        $select->join(
            "BNF2_Demanda_Segmentos",
            "BNF2_Demanda.id = BNF2_Demanda_Segmentos.BNF2_Demanda_id",
            array()
        );
        $select->join(
            "BNF2_Segmentos",
            "BNF2_Demanda_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id",
            array()
        );
        $select->join(
            "BNF2_Campanias",
            "BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id",
            array("Campania" => 'NombreCampania')
        );
        $select->join(
            "BNF2_Demanda_Rubros",
            "BNF2_Demanda_Rubros.BNF2_Demanda_id = BNF2_Demanda.id",
            array()
        );
        $select->join(
            "BNF_Rubro",
            "BNF_Rubro.id = BNF2_Demanda_Rubros.BNF_Rubro_id",
            array("Rubro" => "Nombre")
        );
        $select->join(
            "BNF2_Demanda_EmpresasAdicionales",
            "BNF2_Demanda_EmpresasAdicionales.BNF2_Demanda_id = BNF2_Demanda.id",
            array("EmpresasAdicionales" => "NombreEmpresa")
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF2_Demanda.BNF_Empresa_id", $empresa);
        }

        if (!empty($fecha)) {
            $select->where->equalTo("FechaDemanda", $fecha);
        }

        if (!empty($demanda)) {
            $select->where->equalTo("BNF2_Campanias.id", $demanda);
        }

        $select->where->equalTo("BNF2_Demanda_Segmentos.Eliminado", 0);
        $select->where->equalTo("BNF2_Demanda_Rubros.Eliminado", 0);
        $select->group("BNF2_Demanda.id");

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveDemanda(Demanda $demanda)
    {
        $data = array(
            'BNF_Empresa_id' => $demanda->BNF_Empresa_id,
            'FechaDemanda' => $demanda->FechaDemanda,
            'PrecioMinimo' => $demanda->PrecioMinimo,
            'PrecioMaximo' => $demanda->PrecioMaximo,
            'Target' => $demanda->Target,
            'Comentarios' => $demanda->Comentarios,
            'Actualizaciones' => $demanda->Actualizaciones,
            'Eliminado' => $demanda->Eliminado
        );

        $id = (int)$demanda->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getDemanda($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Demanda id does not exist');
            }
        }
        return $id;
    }

    public function deleteDemanda($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
