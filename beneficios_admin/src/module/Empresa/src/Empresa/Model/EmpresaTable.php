<?php

namespace Empresa\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class EmpresaTable
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

    public function getEmpresasClienteNormExist($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array(),
            'left'
        );
        $select->join(
            'BNF_TipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array()
        );
        $select->where(
            'BNF_TipoEmpresa.Nombre = "Cliente" AND BNF_Empresa.id = ' . $id .
            ' AND ClaseEmpresaCliente = "Normal"'
        );
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getEmpresasClienteEspExist($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array(),
            'left'
        );
        $select->join('BNF_TipoEmpresa', 'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id', array());
        $select->where(
            'BNF_TipoEmpresa.Nombre = "Cliente" AND BNF_Empresa.id = ' . $id .
            ' AND ClaseEmpresaCliente = "Especial"'
        );
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getEmpresasCliente()
    {
        $resultSet = $this->tableGateway->select('ClaseEmpresaCliente IS NOT NULL AND Cliente = 1');
        $resultSet->buffer();
        return $resultSet;
    }

    public function getEmpresaVerisure($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(array('id' => $id));
        return $resultSet->buffer();
    }
    public function getEmpresa($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            $row = 0;
        }
        return $row;
    }


    public function getEmpresaProvReport()
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->columns( array(
            'id' => 'id',
            'NombreComercial' => 'NombreComercial',
            'RazonSocial' => 'RazonSocial',
            'Ruc' => 'Ruc'
        ));
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array(),
            'left'
        );
        $select->join('BNF_TipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array());
        $select->where('BNF_TipoEmpresa.Nombre = "Proveedor" AND BNF_EmpresaTipoEmpresa.Eliminado = 0');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getEmpresaProv()
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array(),
            'left'
        );
        $select->join('BNF_TipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array());
        $select->where('BNF_TipoEmpresa.Nombre = "Proveedor" AND BNF_EmpresaTipoEmpresa.Eliminado = 0');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresaCli()
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array(),
            'left'
        );
        $select->join('BNF_TipoEmpresa', 'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id', array());
        $select->where(
            'BNF_TipoEmpresa.Nombre = "Cliente" AND BNF_EmpresaTipoEmpresa.Eliminado = 0' .
            ' AND ClaseEmpresaCliente IS NOT NULL'
        );
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresaCliNorm($razonSocial = null, $ruc = null)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join('BNF_EmpresaTipoEmpresa', 'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id', array());
        $select->join('BNF_TipoEmpresa', 'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id', array());

        if ($razonSocial == null and $ruc != null) {
            $select->where("(BNF_Empresa.Ruc like '%" . $ruc . "%')");
        } elseif ($razonSocial != null and $ruc == null) {
            $select->where("(BNF_Empresa.id = '" . $razonSocial . "')");
        } elseif ($razonSocial != null and $ruc != null) {
            $select->where("(BNF_Empresa.id = '" . $razonSocial . "' OR BNF_Empresa.Ruc like '%" . $ruc . "%')");
        }

        $select->where(
            'BNF_TipoEmpresa.Nombre = "Cliente" AND BNF_EmpresaTipoEmpresa.Eliminado=0 AND ClaseEmpresaCliente="Normal"'
        );
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresaCliEsp($razonSocial = null, $ruc = null)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join('BNF_EmpresaTipoEmpresa', 'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id', array());
        $select->join('BNF_TipoEmpresa', 'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id', array());

        if ($razonSocial == null and $ruc != null) {
            $select->where("(BNF_Empresa.Ruc like '%" . $ruc . "%')");
        } elseif ($razonSocial != null and $ruc == null) {
            $select->where("(BNF_Empresa.id = '" . $razonSocial . "')");
        } elseif ($razonSocial != null and $ruc != null) {
            $select->where("(BNF_Empresa.id = '" . $razonSocial . "' OR BNF_Empresa.Ruc like '%" . $ruc . "%')");
        }

        $select->where(
            'BNF_TipoEmpresa.Nombre = "Cliente" AND BNF_EmpresaTipoEmpresa.Eliminado=0' .
            ' AND ClaseEmpresaCliente="Especial"'
        );
        //echo $select->getSqlString();
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresabyRuc($ruc)
    {
        $value = (int)$ruc;
        $rowset = $this->tableGateway->select(array('Ruc' => $value));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find ruc $value");
        }
        return $row;
    }

    public function getValidateRuc($documento, $idvalue = null)
    {
        $id = (int)$idvalue;
        if ($id == 0) {
            $rowset = $this->tableGateway->select(array('Ruc' => $documento));
        } else {
            $rowset = $this->tableGateway->select(array('Ruc' => $documento, 'id != ' . $id));
        }

        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function getDetailEmpresa($razonSocial = "", $ruc = "", $order_by = null, $order = null)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->columns(array('*'));
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array('TEliminado' => 'Eliminado'),
            'left'
        );
        $select->join(
            'BNF_TipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array('NombreTipoEmpresa' => 'Nombre'),
            'left'
        );
        $select->join(
            'BNF_Usuario',
            'BNF_Empresa.BNF_Usuario_id = BNF_Usuario.id',
            array(
                'CNombres' => 'Nombres',
                'CApellidos' => 'Apellidos'
            ),
            'left'
        );
        $select->join(
            'BNF_TipoDocumento',
            'BNF_Empresa.BNF_TipoDocumento_id = BNF_TipoDocumento.id',
            array('TipoDocumento' => 'Nombre'),
            'left'
        );
        $select->join(
            array('e' => 'BNF_Ubigeo'),
            'BNF_Empresa.BNF_Ubigeo_id_envio = e.id',
            array('Envio' => 'Nombre'),
            'left'
        );
        $select->join(
            array('l' => 'BNF_Ubigeo'),
            'BNF_Empresa.BNF_Ubigeo_id_legal = l.id',
            array('Legal' => 'Nombre'),
            'left'
        );
        if ($razonSocial != "" and $ruc != "") {
            $select->where("BNF_Empresa.id = '" . $razonSocial . "' OR BNF_Empresa.Ruc like '%" . $ruc . "%'");
        } elseif ($razonSocial != "" and $ruc == "") {
            $select->where("BNF_Empresa.id = '" . $razonSocial . "'");
        } else {
            $select->where("BNF_Empresa.Ruc like '%" . $ruc . "%'");
        }
        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF_Empresa.id $order");
        }
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getReport()
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->columns(array('*'));
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array('TEliminado' => 'Eliminado'),
            'left'
        );
        $select->join(
            'BNF_TipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array('NombreTipoEmpresa' => 'Nombre'),
            'left'
        );
        $select->join(
            'BNF_Usuario',
            'BNF_Empresa.BNF_Usuario_id = BNF_Usuario.id',
            array('CNombres' => 'Nombres',
                'CApellidos' => 'Apellidos'),
            'left'
        );
        $select->join(
            'BNF_TipoDocumento',
            'BNF_Empresa.BNF_TipoDocumento_id = BNF_TipoDocumento.id',
            array('TipoDocumento' => 'Nombre'),
            'left'
        );
        $select->order("BNF_Empresa.id DESC");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function saveEmpresa(Empresa $empresa, $cliente)
    {
        $data = $empresa->getArrayCopy();
        unset($data['CNombres']);
        unset($data['CApellidos']);
        unset($data['TipoDocumento']);
        unset($data['TipoEmpresa']);
        unset($data['NombreTipoEmpresa']);
        unset($data['TEliminado']);
        unset($data['inputFilter']);
        unset($data['ClaseEmpresaCliente']);
        unset($data['CantidadClientes']);
        $data['SubDominio'] = trim($data['SubDominio']);

        if ($data['Logo'] == null) {
            unset($data['Logo']);
        }
        if ($data['Logo_sitio'] == null) {
            unset($data['Logo_sitio']);
        }

        if ($cliente) {
            $data['ClaseEmpresaCliente'] = $empresa->ClaseEmpresaCliente;
        }

        $id = (int)$empresa->id;

        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresa($id)) {
                unset($data['FechaCreacion']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Empresa id does not exist');
            }
        }
    }

    public function deleteEmpresaTipo($id, $val, $tipo)
    {
        if ($tipo == 1) {
            $data['Proveedor'] = (int)$val;
        } else {
            $data['Cliente'] = (int)$val;
        }

        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getPaqueteEmpresas()
    {
        $select = new Select();
        $select->from('BNF_PaqueteEmpresaProveedor');
        $select->columns(
            array('id' => 'BNF_Empresa_id')
        );
        $select->join(
            'BNF_Empresa',
            'BNF_PaqueteEmpresaProveedor.BNF_Empresa_id = BNF_Empresa.id',
            array(
                'NombreComercial' => 'NombreComercial',
                'RazonSocial' => 'RazonSocial',
                'Ruc' => 'Ruc'
            )
        );
        $select->join('BNF_EmpresaTipoEmpresa', 'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id', array());
        $select->where("BNF_EmpresaTipoEmpresa.Eliminado=0 AND BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id=1");
        $select->group('id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresaProvActiva($id)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array('TEliminado' => 'Eliminado')
        );
        $select->join(
            'BNF_TipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array()
        );
        $select->where->equalTo('BNF_TipoEmpresa.Nombre', "Proveedor")
            ->and->equalTo('BNF_Empresa.id', $id)
            ->and->equalTo('BNF_Empresa.Proveedor', 1);
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getEmpresaCliActiva($id)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array('TEliminado' => 'Eliminado')
        );
        $select->join(
            'BNF_TipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_TipoEmpresa_id = BNF_TipoEmpresa.id',
            array()
        );
        $select->where->equalTo('BNF_TipoEmpresa.Nombre', "Cliente")
            ->and->equalTo('BNF_Empresa.id', $id)
            ->and->equalTo('BNF_Empresa.Cliente', 1);
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getIfExistSlug($slug)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->where->like('Slug', $slug . "%");
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getTotalClientes($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array()
        );
        $select->where->equalTo('BNF_TipoEmpresa_id', 2);
        $select->where->equalTo('Cliente', 1);
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
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getTotalProveedoras($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_Empresa.id = BNF_EmpresaTipoEmpresa.BNF_Empresa_id',
            array()
        );
        $select->where->equalTo('Proveedor', 1);
        $select->where->equalTo('BNF_TipoEmpresa_id', 1);
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
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getCantClientesEmpresa($id = 0, $order_by = null, $order = null)
    {
        $select = new Select();
        $select->from(array('e' => 'BNF_Empresa'));
        $select->columns(
            array(
                'NombreComercial',
                'CantidadClientes' => new Expression("(SELECT COUNT(*) FROM BNF_Cliente AS c
                                             INNER JOIN BNF_EmpresaClienteCliente AS cc ON c.id = cc.BNF_Cliente_id
                                             WHERE cc.BNF_Empresa_id = e.id)")
            )
        );
        if ($id > 0) {
            $select->where->equalTo('e.id', (int)$id);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("e.id $order");
        }

        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getSubDominio($subdominio, $id = null)
    {
        if ($id == null) {
            $rowset = $this->tableGateway->select(array('SubDominio' => $subdominio));
        } else {
            $rowset = $this->tableGateway->select(array('SubDominio' => $subdominio, 'id !=' . (int)$id));
        }
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }
}
