<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/09/15
 * Time: 12:38 AM
 */
namespace Paquete\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class PaqueteTable
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

    public function getPaquete($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return $row;
    }

    public function getPaquetes()
    {
        $select = new Select();
        $select->from('BNF_Paquete');

        $select->join(
            'BNF_TipoPaquete',
            'BNF_Paquete.BNF_TipoPaquete_id = BNF_TipoPaquete.id',
            array('NombreTipoPaquete' => 'NombreTipoPaquete')
        );
        $select->where(array('Eliminado' => 0));
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDetailPaqueteEdit($id)
    {
        $select = new Select();
        $select->from('BNF_Paquete');
        $select->join(
            'BNF_PaquetePais',
            'BNF_Paquete.id = BNF_PaquetePais.BNF_Paquete_id',
            array('NombrePais' => 'BNF_Pais_id')
        );
        $select->where('BNF_Paquete.id = ' . $id);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDetailPaquete(
        $pais = null,
        $tipo = null,
        $inicio = null,
        $fin = null,
        $order_by = null,
        $order = 'DESC'
    ) {
        $select = new Select();
        $select->from(array('p' => 'BNF_Paquete'));
        $select->columns(
            array('*',
                'Cantidad' => new Expression(
                    "IF(p.NumeroDias != 'NULL',p.NumeroDias, p.CantidadDescargas+p.Bonificacion)"
                ),
            )
        );
        $select->join(
            array('tp' => 'BNF_TipoPaquete'),
            'p.BNF_TipoPaquete_id = tp.id',
            array('NombreTipoPaquete' => 'NombreTipoPaquete')
        );
        $select->join(array('pp' => 'BNF_PaquetePais'), 'pp.BNF_Paquete_id = p.id', array());
        $select->join(array('pa' => 'BNF_Pais'), 'pp.BNF_Pais_id = pa.id', array());
        if ($inicio != null || $fin != null) {
            if ($inicio == null) {
                $inicio = '1900-01-01';
            }
            if ($fin == null) {
                $fin = date("Y-m-d");
            }
            $select->where(
                "p.FechaCreacion BETWEEN '$inicio' AND ADDDATE('$fin', INTERVAL 1 DAY)"
            );
        } elseif ($pais != null || $tipo != null) {
            $select->where
                ->equalTo('p.BNF_TipoPaquete_id', $tipo)
                ->or
                ->equalTo('pa.id', $pais);
        }

        if (isset($order_by) && $order_by != "" && $order_by != "id") {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order('p.FechaCreacion DESC');
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getExportPaquete()
    {
        $select = new Select();
        $select->from(array('p' => 'BNF_Paquete'));
        $select->join(
            array('tp' => 'BNF_TipoPaquete'),
            'p.BNF_TipoPaquete_id = tp.id',
            array('NombreTipoPaquete' => 'NombreTipoPaquete')
        );
        $select->join(array('pp' => 'BNF_PaquetePais'), 'pp.BNF_Paquete_id = p.id', array());
        $select->join(array('pa' => 'BNF_Pais'), 'pp.BNF_Pais_id = pa.id', array('NombrePais' => 'NombrePais'));
        $select->order('p.FechaCreacion DESC');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function savePaquete(Paquete $paquete, $tipo)
    {
        $data = array(
            'BNF_TipoPaquete_id' => $paquete->BNF_TipoPaquete_id,
            'Nombre' => $paquete->Nombre,
            'Eliminado' => (int)$paquete->Eliminado,
        );
        if ($tipo == 1) {
            $data += array(
                'Precio' => (real)$paquete->Precio,
                'NumeroDias' => null,
                'CostoDia' => null,
                'CantidadDescargas' => (int)$paquete->CantidadDescargas,
                'PrecioUnitarioDescarga' => (real)$paquete->PrecioUnitarioDescarga,
                'Bonificacion' => (int)$paquete->Bonificacion,
                'PrecioUnitarioBonificacion' => (real)$paquete->PrecioUnitarioBonificacion,
                'Bolsa' => (int)$paquete->CantidadDescargas + (int)$paquete->Bonificacion,
            );
        } elseif ($tipo == 2) {
            $data += array(
                'Precio' => (real)$paquete->Precio,
                'CantidadDescargas' => null,
                'PrecioUnitarioDescarga' => null,
                'Bonificacion' => null,
                'PrecioUnitarioBonificacion' => null,
                'NumeroDias' => (int)$paquete->NumeroDias,
                'CostoDia' => (real)$paquete->CostoDia,
                'Bolsa' => (int)$paquete->NumeroDias,
            );
        } elseif ($tipo == 3) {
            $data += array(
                'Precio' => null,
                'NumeroDias' => null,
                'CostoDia' => null,
                'CantidadDescargas' => null,
                'PrecioUnitarioDescarga' => null,
                'Bonificacion' => null,
                'PrecioUnitarioBonificacion' => null,
                'Bolsa' => null,
            );
        }
        $id = (int)$paquete->id;

        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getPaquete($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Paquete id does not exist');
            }
        }
        return $id;
    }

    public function deletePaquete($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getPaqueteNombre()
    {
        $select = new Select();
        $select->from('BNF_Paquete');
        $select->columns(array('id','Nombre'));
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
