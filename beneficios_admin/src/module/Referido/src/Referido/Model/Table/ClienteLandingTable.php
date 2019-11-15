<?php

namespace Referido\Model\Table;

use Referido\Model\ClienteLanding;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;

class ClienteLandingTable
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
            return false;
        }
        return $row;
    }

    public function getClientByDoc($documento)
    {

        $rowset = $this->tableGateway->select(array('Documento' => $documento));
        $row = $rowset->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCliente(ClienteLanding $cliente)
    {
        $data = array(
            'Nombres_Apellidos' => $cliente->Nombres_Apellidos,
            'Telefonos' => $cliente->Telefonos,
            'Email' => $cliente->Email,
            'Especialista' => $cliente->Especialista,
            'Documento' => $cliente->Documento,
            'Tipo' => $cliente->Tipo
        );

        $id = (int)$cliente->id;

        if ($id == 0) {
            $data['Creado'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        }
        return $id;
    }

    public function update($data, $id)
    {
        $this->tableGateway->update($data, array('id' => $id));
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

    public function getAllClients($searchClient = null, $searchDateIni = null, $searchDateEnd = null, $order_by = "", $order = "")
    {
        $select = new Select();
        $select->from("BNF4_LandingClientesColaboradores");
        $select->columns(
            array(
                "*",
                'FechaAsignacion' => new Expression(
                    "(SELECT 
                        BNF2_Asignacion_Puntos_Estado_Log.FechaCreacion
                    FROM
                        BNF2_Asignacion_Puntos_Estado_Log
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Segmentos.id = BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias_Empresas.BNF2_Campania_id = BNF2_Segmentos.BNF2_Campania_id
                    WHERE
                        BNF_Cliente_id = BNF_Cliente.id
                            AND BNF2_Campanias_Empresas.BNF_Empresa_id = 369
                            AND Estado_Cron = 0
                    ORDER BY BNF2_Asignacion_Puntos_Estado_Log.FechaCreacion DESC
                    LIMIT 1)"
                ),
                'PuntosAsignados' => new Expression(
                    "(SELECT 
                        SUM(CantidadPuntosDisponibles)
                    FROM
                        BNF2_Asignacion_Puntos
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id
                            INNER JOIN
                        BNF2_Campanias_Empresas ON BNF2_Campanias_Empresas.BNF2_Campania_id = BNF2_Segmentos.BNF2_Campania_id                
                    WHERE
                        BNF_Cliente_id = BNF_Cliente.id
                            AND BNF2_Campanias_Empresas.BNF_Empresa_id = 369
                            AND BNF2_Asignacion_Puntos.EstadoPuntos != 'Cancelado')")
            )
        );

        $select->join(
            "BNF_Cliente",
            "BNF4_LandingClientesColaboradores.Documento = BNF_Cliente.NumeroDocumento",
            array()
        );

        $select->join(
            "BNF4_LandingReferidos",
            "BNF4_LandingReferidos.cliente_id = BNF4_LandingClientesColaboradores.id",
            array()
        );

        if (!empty($searchClient)) {
            $select->where->literal(
                "((BNF4_LandingClientesColaboradores.Documento LIKE '%" . $searchClient . "%'" .
                " OR BNF4_LandingClientesColaboradores.Nombres_Apellidos LIKE '%" . $searchClient . "%'))");
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF4_LandingClientesColaboradores.id DESC");
        }

        if ($searchDateIni != '' and $searchDateEnd != '') {
            $select->where->addPredicate(
                new Expression("date(BNF4_LandingClientesColaboradores.Creado) BETWEEN '$searchDateIni' AND '$searchDateEnd'")
            );
        }

        $select->group('BNF4_LandingClientesColaboradores.id');
        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
}
