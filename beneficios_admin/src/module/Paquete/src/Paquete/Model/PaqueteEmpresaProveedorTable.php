<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/09/15
 * Time: 12:32 PM
 */

namespace Paquete\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class PaqueteEmpresaProveedorTable
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

    public function getPaqueteProv($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getPaqueteProvEdit($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_PaqueteEmpresaProveedor');
        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF_PaqueteEmpresaProveedor.BNF_Empresa_id',
            array('NombreComercial' => 'NombreComercial'
            )
        );
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array('TEliminado' => 'Eliminado'),
            'left'
        );
        $select->join('BNF_Paquete', 'BNF_Paquete.id = BNF_PaqueteEmpresaProveedor.BNF_Paquete_id', array());
        $select->join('BNF_PaquetePais', 'BNF_Paquete.id = BNF_PaquetePais.BNF_Paquete_id', array());
        $select->join('BNF_Pais', 'BNF_Pais.id = BNF_PaquetePais.BNF_Pais_id', array('NombrePais' => 'NombrePais'));
        $select->where(array('BNF_PaqueteEmpresaProveedor.id' => $id));

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPaqueteProvExport()
    {
        $select = new Select();
        $select->from('BNF_PaqueteEmpresaProveedor');
        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF_PaqueteEmpresaProveedor.BNF_Empresa_id',
            array('NombreComercial' => 'NombreComercial')
        );
        $select->join(
            array('tp' => 'BNF_TipoPaquete'),
            'tp.id=BNF_PaqueteEmpresaProveedor.BNF_TipoPaquete_id',
            array('TipoPaquete' => 'NombreTipoPaquete')
        );
        $select->join(
            array('bt' => 'BNF_BolsaTotal'),
            'bt.BNF_Empresa_id=BNF_Empresa.id AND bt.BNF_TipoPaquete_id=tp.id',
            array('BolsaActual' => 'BolsaActual')
        );
        $select->join(array('p' => 'BNF_Paquete'), 'BNF_PaqueteEmpresaProveedor.BNF_Paquete_id = p.id', array());
        $select->join('BNF_PaquetePais', 'p.id = BNF_PaquetePais.BNF_Paquete_id', array());
        $select->join('BNF_Pais', 'BNF_Pais.id = BNF_PaquetePais.BNF_Pais_id', array('NombrePais' => 'NombrePais'));
        $select->join(
            'BNF_Usuario',
            'BNF_Usuario.id = BNF_PaqueteEmpresaProveedor.BNF_Usuario_id',
            array('Nombres' => 'Nombres', 'Apellidos' => 'Apellidos')
        );
        $select->order('BNF_PaqueteEmpresaProveedor.FechaCreacion DESC');

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function savePaqueteProv(PaqueteEmpresaProveedor $paqueteEmpresaProveedor, $accion, $tipo)
    {
        if ($accion == 1) {
            $data = array(
                'BNF_Paquete_id' => $paqueteEmpresaProveedor->BNF_Paquete_id,
                'BNF_Empresa_id' => $paqueteEmpresaProveedor->BNF_Empresa_id,
                'BNF_Usuario_id' => $paqueteEmpresaProveedor->BNF_Usuario_id,
                'NombrePaquete' => $paqueteEmpresaProveedor->NombrePaquete,
                'BNF_TipoPaquete_id' => $paqueteEmpresaProveedor->BNF_TipoPaquete_id,
                'FechaCompra' => $paqueteEmpresaProveedor->FechaCompra,
                'CostoPorLead' => $paqueteEmpresaProveedor->CostoPorLead,
                'MaximoLeads' => $paqueteEmpresaProveedor->MaximoLeads,
                'Factura' => $paqueteEmpresaProveedor->Factura,
                'Precio' => $paqueteEmpresaProveedor->Precio,
                'CantidadDescargas' => $paqueteEmpresaProveedor->CantidadDescargas,
                'PrecioUnitarioDescarga' => $paqueteEmpresaProveedor->PrecioUnitarioDescarga,
                'Bonificacion' => $paqueteEmpresaProveedor->Bonificacion,
                'PrecioUnitarioBonificacion' => $paqueteEmpresaProveedor->PrecioUnitarioBonificacion,
                'NumeroDias' => $paqueteEmpresaProveedor->NumeroDias,
                'CostoDia' => $paqueteEmpresaProveedor->CostoDia,
                'Bolsa' => $paqueteEmpresaProveedor->Bolsa,
                'Eliminado' => $paqueteEmpresaProveedor->Eliminado,
            );
        } elseif ($accion == 2) {
            $data = array(
                'BNF_Usuario_id' => $paqueteEmpresaProveedor->BNF_Usuario_id,
                'FechaCompra' => $paqueteEmpresaProveedor->FechaCompra,
                'CostoPorLead' => $paqueteEmpresaProveedor->CostoPorLead,
                'MaximoLeads' => $paqueteEmpresaProveedor->MaximoLeads,
                'Factura' => $paqueteEmpresaProveedor->Factura,
            );
        }

        $id = (int)$paqueteEmpresaProveedor->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $data['Eliminado'] = '0';
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getPaqueteProv($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                if ($tipo == 3) {
                    $data['Bolsa'] = $paqueteEmpresaProveedor->Bolsa;
                }
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Paquete id does not exist');
            }
        }
    }

    public function deletePaqueteProv($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getDetailPaquete(
        $pais = null,
        $tipo = null,
        $nombre = null,
        $inicio = null,
        $fin = null,
        $order_by = null,
        $order = null
    )
    {
        $select = new Select();
        $select->from(array('pep' => 'BNF_PaqueteEmpresaProveedor'));
        $select->columns(
            array('*',
                'Cantidad' => new Expression(
                    " IF(pep.MaximoLeads != 'NULL',pep.MaximoLeads,"
                    . "IF(pep.NumeroDias !='NULL' ,pep.NumeroDias, pep.CantidadDescargas+pep.Bonificacion))"
                ),
                'Precio' => new Expression(
                    " IF(pep.CostoPorLead != 'NULL',pep.CostoPorLead,pep.Precio)"
                )
            )
        );
        $select->join(
            array('e' => 'BNF_Empresa'),
            'pep.BNF_Empresa_id = e.id',
            array('NombreComercial' => 'NombreComercial')
        );
        $select->join(array('p' => 'BNF_Paquete'), 'pep.BNF_Paquete_id = p.id', array());
        $select->join(
            array('tp' => 'BNF_TipoPaquete'),
            'tp.id=pep.BNF_TipoPaquete_id',
            array('TipoPaquete' => 'NombreTipoPaquete')
        );
        $select->join(
            array('bt' => 'BNF_BolsaTotal'),
            'bt.BNF_Empresa_id=e.id AND bt.BNF_TipoPaquete_id=tp.id',
            array('BolsaActual' => 'BolsaActual')
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
                "pep.FechaCreacion BETWEEN '$inicio' AND ADDDATE('$fin', INTERVAL 1 DAY)"
            );
        } elseif ($pais != null || $tipo != null || $nombre != null) {
            $select->where
                ->equalTo('pep.BNF_Empresa_id', $nombre)
                ->or
                ->equalTo('pep.BNF_TipoPaquete_id', $tipo)
                ->or
                ->equalTo('pa.id', $pais);
        }

        if (isset($order_by) && $order_by != "" && $order_by != "id") {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order('pep.FechaCreacion DESC');
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function deletePaqueteProvXEmpresa($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF_Empresa_id' => (int)$id));
    }

    public function getTotalPaqueteEmpresas($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_PaqueteEmpresaProveedor');
        $select->where('BNF_Empresa_id = ' . $id);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getPaqueteProvxTipo($empresa, $tipo)
    {
        $select = new Select();
        $select->from('BNF_PaqueteEmpresaProveedor');
        $select->join('BNF_Paquete', 'BNF_Paquete.id = BNF_PaqueteEmpresaProveedor.BNF_Paquete_id', array());
        $select->where
            ->equalTo('BNF_PaqueteEmpresaProveedor.BNF_Empresa_id', $empresa)
            ->and
            ->equalTo('BNF_PaqueteEmpresaProveedor.BNF_TipoPaquete_id', $tipo);
        $select->order('BNF_PaqueteEmpresaProveedor.id DESC');
        $select->limit(1);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPaquetesComprados($fechainicio, $fechafin, $paquete = null, $factura = null, $empresa_id = 0)
    {
        $select = new Select();
        $select->from('BNF_PaqueteEmpresaProveedor');
        $select->columns(
            array('*',
                'FechaCompra' => new Expression('DATE_FORMAT(FechaCompra,\'%Y-%m-%d\')'),
                'Cantidad' => new Expression(
                    " IF(MaximoLeads != 'NULL',MaximoLeads,"
                    . "IF(NumeroDias !='NULL' ,NumeroDias, CantidadDescargas + Bonificacion))"
                ),
                'Precio' => new Expression(
                    " IF(CostoPorLead != 'NULL',CostoPorLead,Precio)"
                )
            )
        );
        $select->join(
            'BNF_TipoPaquete',
            'BNF_PaqueteEmpresaProveedor.BNF_TipoPaquete_id = BNF_TipoPaquete.id',
            array('TipoPaquete' => 'NombreTipoPaquete')
        );
        $select->join(
            'BNF_Usuario',
            'BNF_Usuario.id = BNF_PaqueteEmpresaProveedor.BNF_Usuario_id',
            array('Apellidos', 'Nombres')
        );

        if (!empty($fechainicio) or !empty($fechafin)) {
            $select->where->between('FechaCompra', $fechainicio, $fechafin);
        }

        if ($paquete != null) {
            $select->where->or->equalTo('BNF_Paquete_id', $paquete);
        }
        if ($factura != null) {
            $select->where->or->equalTo('Factura', $factura);
        }
        if ((int)$empresa_id != 0) {
            $select->where->equalTo('BNF_PaqueteEmpresaProveedor.BNF_Empresa_id', $empresa_id);
        }
        $select->order('id ASC');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getTotalPaquetesComprados($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from('BNF_PaqueteEmpresaProveedor');
        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getDetallePaquetesCompradosLead($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from(array('PEP' => 'BNF_PaqueteEmpresaProveedor'));
        $select->columns(
            array(
                'NombrePaquete',
                'CostoPorLead',
                'BNF_Empresa_id'
            )
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = PEP.BNF_Empresa_id',
            array('NombreComercial')
        );
        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "PEP.FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }
        $select->where->equalTo('BNF_TipoPaquete_id', 3);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
