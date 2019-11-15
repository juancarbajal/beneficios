<?php

namespace Cliente\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;

class ClienteTable
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

    public function getCliente($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not fin row $id");
        }
        return $row;
    }

    public function getDocumento($documento)
    {
        $select = new Select();
        $select->from('BNF_Cliente');
        $select->columns(array("*"));
        $select->join(
            'BNF_EmpresaClienteCliente',
            'BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id',
            array('Estado'),
            'left'
        );
        $select->where
            ->equalTo('BNF_Cliente.NumeroDocumento', $documento)
            ->and
            ->equalTo('BNF_EmpresaClienteCliente.Eliminado', 0);
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function getDocumentoId($documento, $id)
    {
        $select = new Select();
        $select->from('BNF_Cliente');
        $select->columns(array("*"));
        $select->join(
            'BNF_EmpresaClienteCliente',
            'BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id',
            array('Estado'),
            'left'
        );
        $select->where
            ->equalTo('NumeroDocumento', $documento)
            ->and
            ->notEqualTo('BNF_Cliente.id', $id)
            ->and
            ->isNotNull('BNF_EmpresaClienteCliente.BNF_Empresa_id')
            ->and
            ->notEqualTo('BNF_EmpresaClienteCliente.Eliminado', 1);
        $resultSet = $this->tableGateway->selectWith($select);
        //echo $select->getSqlString(); exit;
        $row = $resultSet->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function getClientIfExist($documento)
    {
        $rowset = $this->tableGateway->select(array('NumeroDocumento' => $documento));
        $row = $rowset->current();
        return !$row ? false : true;
    }

    public function getClientByDoc($documento)
    {
        $rowset = $this->tableGateway->select(array('NumeroDocumento' => $documento));
        $row = $rowset->current();
        return !$row ? false : $row;
    }

    public function getDetailCliente()
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->columns(array('idEmpresa' => 'id', 'NombreComercial' => 'RazonSocial'));
        $select->join('BNF_EmpresaSegmento', 'BNF_Empresa.id = BNF_EmpresaSegmento.BNF_Empresa_id', array());
        $select->join('BNF_EmpresaSubgrupo', 'BNF_Empresa.id = BNF_EmpresaSubgrupo.BNF_Empresa_id', array());
        $select->join(
            'BNF_EmpresaSegmentoCliente',
            'BNF_EmpresaSegmento.id = BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id',
            array()
        );
        $select->join(
            'BNF_EmpresaSubgrupoCliente',
            'BNF_EmpresaSubgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_EmpresaSubgrupo_id',
            array()
        );
        $select->join(
            'BNF_Cliente',
            'BNF_EmpresaSegmentoCliente.BNF_Cliente_id = BNF_Cliente.id' .
            ' AND BNF_EmpresaSubgrupoCliente.BNF_Cliente_id = BNF_Cliente.id',
            array(
                'id' => 'id',
                'Nombre',
                'Apellido',
                'NumeroDocumento',
                'Genero',
                'FechaNacimiento',
                'Eliminado'
            )
        );
        $select->join(
            'BNF_Segmento',
            'BNF_EmpresaSegmento.BNF_Segmento_id =  BNF_Segmento.id',
            array('NombreSegmento' => 'Nombre')
        );
        $select->join(
            'BNF_Subgrupo',
            'BNF_EmpresaSubgrupo.BNF_Subgrupo_id =  BNF_Subgrupo.id',
            array('NombreSubgrupo' => 'Nombre')
        );
        $select->order(array('BNF_Cliente.id DESC'));
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDetailClienteSeach($cliente, $empresa)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->columns(array('idEmpresa' => 'id', 'NombreComercial' => 'RazonSocial'));
        $select->join('BNF_EmpresaSegmento', 'BNF_Empresa.id = BNF_EmpresaSegmento.BNF_Empresa_id', array());
        $select->join('BNF_EmpresaSubgrupo', 'BNF_Empresa.id = BNF_EmpresaSubgrupo.BNF_Empresa_id', array());
        $select->join(
            'BNF_EmpresaSegmentoCliente',
            'BNF_EmpresaSegmento.id = BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id',
            array()
        );
        $select->join(
            'BNF_EmpresaSubgrupoCliente',
            'BNF_EmpresaSubgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_EmpresaSubgrupo_id',
            array()
        );
        $select->join(
            'BNF_Cliente',
            'BNF_EmpresaSegmentoCliente.BNF_Cliente_id = BNF_Cliente.id'
            . ' AND BNF_EmpresaSubgrupoCliente.BNF_Cliente_id = BNF_Cliente.id',
            array(
                'id' => 'id',
                'Nombre',
                'Apellido',
                'NumeroDocumento',
                'Genero',
                'FechaNacimiento',
                'Eliminado'
            )
        );
        $select->join(
            'BNF_Segmento',
            'BNF_EmpresaSegmento.BNF_Segmento_id =  BNF_Segmento.id',
            array('NombreSegmento' => 'Nombre')
        );
        $select->join(
            'BNF_Subgrupo',
            'BNF_EmpresaSubgrupo.BNF_Subgrupo_id =  BNF_Subgrupo.id',
            array('NombreSubgrupo' => 'Nombre'),
            'left'
        );
        $select->where
            ->like('BNF_Cliente.Nombre', '%' . $cliente . '%')
            ->or
            ->like('BNF_Cliente.Apellido', '%' . $cliente . '%')
            ->or
            ->like('BNF_Cliente.NumeroDocumento', '%' . $cliente . '%');
        $select->where
            ->like('BNF_Empresa.NombreComercial', '%' . $empresa . '%')
            ->or
            ->like('BNF_Empresa.RazonSocial', '%' . $empresa . '%')
            ->or
            ->like('BNF_Empresa.RUC', '%' . $empresa . '%');
        $select->order(array('BNF_Cliente.id DESC'));
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getDetailClienteEmpresa($id)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->columns(array('NombreComercial' => 'id'));
        $select->join(
            'BNF_EmpresaClienteCliente',
            'BNF_Empresa.id = BNF_EmpresaClienteCliente.BNF_Empresa_id',
            array('Estado'),
            'inner'
        );
        $select->join(
            'BNF_EmpresaSegmento',
            'BNF_Empresa.id = BNF_EmpresaSegmento.BNF_Empresa_id',
            array('NombreSegmento' => 'BNF_Segmento_id')
        );
        $select->join(
            'BNF_EmpresaSegmentoCliente',
            'BNF_EmpresaSegmento.id = BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id',
            array()
        );
        $select->where("BNF_EmpresaClienteCliente.BNF_Cliente_id = $id");

        $select->where->equalTo("BNF_EmpresaSegmentoCliente.BNF_Cliente_id", $id)
            ->AND->equalTo("BNF_EmpresaSegmentoCliente.Eliminado", 0);
        //$select->where("BNF_EmpresaClienteCliente.Eliminado != 1");
        //echo $select->getSqlString();
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveCliente(Cliente $cliente)
    {
        $data = array(
            'BNF_TipoDocumento_id' => $cliente->BNF_TipoDocumento_id,
            'Nombre' => $cliente->Nombre,
            'Apellido' => $cliente->Apellido,
            'NumeroDocumento' => $cliente->NumeroDocumento,
            'Genero' => $cliente->Genero,
            'FechaNacimiento' => $cliente->FechaNacimiento
        );

        $id = (int)$cliente->id;

        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getCliente($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Cliente id does not exist');
            }
        }
        return $id;
    }

    public function getAllClients($searchClient = null, $searchCompany = null, $order_by = "", $order = "", $tipo = 0)
    {
        $select = new Select();
        $select->from("BNF_Cliente");
        $select->columns(
            array(
                "*",
                "NombreSegmento" => new Expression(
                    "(SELECT 
                        BNF_Segmento.Nombre
                    FROM
                        BNF_Segmento
                            INNER JOIN
                        BNF_EmpresaSegmento ON BNF_EmpresaSegmento.BNF_Segmento_id = BNF_Segmento.id
                            INNER JOIN
                        BNF_EmpresaSegmentoCliente 
                                    ON BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id = BNF_EmpresaSegmento.id
                    WHERE
                        BNF_EmpresaSegmentoCliente.BNF_Cliente_id = BNF_Cliente.id
                            AND BNF_EmpresaSegmento.BNF_Empresa_id = BNF_Empresa.id
                    ORDER BY BNF_EmpresaSegmentoCliente.Eliminado ASC
                    LIMIT 1)"
                ),
                "NombreSubgrupo" => new Expression(
                    "(SELECT 
                        BNF_Subgrupo.Nombre
                    FROM
                        BNF_Subgrupo
                            INNER JOIN
                        BNF_EmpresaSubgrupoCliente ON BNF_Subgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id
                    WHERE
                        BNF_EmpresaSubgrupoCliente.BNF_Cliente_id = BNF_Cliente.id
                            AND BNF_Subgrupo.BNF_Empresa_id = BNF_Empresa.id
                            AND BNF_Subgrupo.Eliminado = 0
                    LIMIT 1)"
                )
            )
        );
        $select->join(
            "BNF_TipoDocumento",
            "BNF_Cliente.BNF_TipoDocumento_id = BNF_TipoDocumento.id",
            array("TipoDocumento" => "Nombre")
        );
        $select->join(
            "BNF_EmpresaClienteCliente",
            "BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id",
            array("Estado")
        );
        $select->join(
            "BNF_Empresa",
            "BNF_Empresa.id = BNF_EmpresaClienteCliente.BNF_Empresa_id",
            array(
                "NombreComercial",
                "ClaseEmpresaCliente",
                "idEmpresa" => "id"
            )
        );

        if (!empty($searchClient) && !empty($searchCompany)) {
            if ($tipo == 7) {
                $select->where->literal(
                    "((BNF_Cliente.NumeroDocumento LIKE '%" . $searchClient . "%'" .
                    " OR BNF_Cliente.Nombre LIKE '%" . $searchClient . "%'" .
                    " OR BNF_Cliente.Apellido LIKE '%" . $searchClient . "%')" .
                    " AND BNF_Empresa.id = " . $searchCompany . ")"
                );
            } else {
                $select->where->literal(
                    "((BNF_Cliente.NumeroDocumento LIKE '%" . $searchClient . "%'" .
                    " OR BNF_Cliente.Nombre LIKE '%" . $searchClient . "%'" .
                    " OR BNF_Cliente.Apellido LIKE '%" . $searchClient . "%')" .
                    " OR BNF_Empresa.id = " . $searchCompany . ")"
                );
            }
        } elseif (!empty($searchClient) && empty($searchCompany)) {
            $select->where->literal(
                "(BNF_Cliente.NumeroDocumento LIKE '%" . $searchClient . "%'" .
                " OR BNF_Cliente.Nombre LIKE '%" . $searchClient . "%'" .
                " OR BNF_Cliente.Apellido LIKE '%" . $searchClient . "%')"
            );
        } elseif (empty($searchClient) && !empty($searchCompany)) {
            $select->where->equalTo("BNF_Empresa.id", $searchCompany);
        }

        $select->where->equalTo("BNF_EmpresaClienteCliente.Eliminado", 0);

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("id DESC");
        }

        $select->group(array("BNF_EmpresaClienteCliente.id"));
        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getReport($empresa = null)
    {
        $select = new Select();
        $select->from("BNF_Cliente");
        $select->columns(
            array(
                "*",
                "NombreSegmento" => new Expression(
                    "(SELECT 
                        BNF_Segmento.Nombre
                    FROM
                        BNF_Segmento
                            INNER JOIN
                        BNF_EmpresaSegmento ON BNF_EmpresaSegmento.BNF_Segmento_id = BNF_Segmento.id
                            INNER JOIN
                        BNF_EmpresaSegmentoCliente 
                                ON BNF_EmpresaSegmentoCliente.BNF_EmpresaSegmento_id = BNF_EmpresaSegmento.id
                    WHERE
                        BNF_EmpresaSegmentoCliente.BNF_Cliente_id = BNF_Cliente.id
                            AND BNF_EmpresaSegmento.BNF_Empresa_id = BNF_Empresa.id
                    ORDER BY BNF_EmpresaSegmentoCliente.Eliminado ASC
                    LIMIT 1)"
                ),
                "NombreSubgrupo" => new Expression(
                    "(SELECT 
                        BNF_Subgrupo.Nombre
                    FROM
                        BNF_Subgrupo
                            INNER JOIN
                        BNF_EmpresaSubgrupoCliente ON BNF_Subgrupo.id = BNF_EmpresaSubgrupoCliente.BNF_Subgrupo_id
                    WHERE
                        BNF_EmpresaSubgrupoCliente.BNF_Cliente_id = BNF_Cliente.id
                            AND BNF_Subgrupo.BNF_Empresa_id = BNF_Empresa.id
                            AND BNF_Subgrupo.Eliminado = 0
                    LIMIT 1)"
                )
            )
        );
        $select->join(
            "BNF_TipoDocumento",
            "BNF_Cliente.BNF_TipoDocumento_id = BNF_TipoDocumento.id",
            array("TipoDocumento" => "Nombre")
        );
        $select->join(
            "BNF_EmpresaClienteCliente",
            "BNF_Cliente.id = BNF_EmpresaClienteCliente.BNF_Cliente_id",
            array("Estado")
        );
        $select->join(
            "BNF_Empresa",
            "BNF_Empresa.id = BNF_EmpresaClienteCliente.BNF_Empresa_id",
            array(
                "NombreComercial",
                "ClaseEmpresaCliente",
                "idEmpresa" => "id"
            )
        );

        if (!empty($empresa)) {
            $select->where->equalTo('BNF_Empresa.id', $empresa);
        }
        $select->where->isNotNull("BNF_EmpresaClienteCliente.BNF_Empresa_id");
        $select->order("BNF_Cliente.FechaCreacion" . " DESC");

        $select->group(array("BNF_EmpresaClienteCliente.id"));
        //echo str_replace('"', '', $select->getSqlString()); exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTotalDniRegistrados($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from("BNF_Cliente");

        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "BNF_Cliente.FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getTotalCorreosRegistrados($fechaini = null, $fechafin = null)
    {
        $select = new Select();
        $select->from("BNF_Cliente");
        $select->join(
            "BNF_ClienteCorreo",
            "BNF_ClienteCorreo.BNF_Cliente_id = BNF_Cliente.id"
        );
        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "BNF_ClienteCorreo.FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function insert($data)
    {
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update($data, $id)
    {
        $this->tableGateway->update($data, array('id' => $id));
    }

    public function delete($where)
    {
        $this->tableGateway->delete($where);
    }

    public function getDuplicates()
    {
        $select = new Select();
        $select->from("BNF_Cliente");
        $select->columns(
            array(
                "Total" => new Expression("COUNT(*)"),
                "NumeroDocumento"
            )
        );
        $select->group("NumeroDocumento");
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getClienteList($documento)
    {
        $resultSet = $this->tableGateway->select(array('NumeroDocumento' => $documento));
        return $resultSet->toArray();
    }

    public function getDuplicatesClients()
    {
        $select = new Select();
        $select->from("BNF_Cliente");
        $select->columns(
            array(
                "Total" => new Expression("COUNT(*)"),
                "NumeroDocumento"
            )
        );
        $select->join(
            "BNF_Preguntas",
            "BNF_Cliente.id = BNF_Preguntas.BNF_Cliente_id",
            array()
        );
        $select->group("NumeroDocumento");
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
