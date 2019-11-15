<?php

namespace Referido\Model\Table;

use Referido\Model\Referido;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;

class ReferidoTable
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

    public function getReferido($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getReferidoByCliente($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('cliente_id' => $id));

        if (!$rowset) {
            return false;
        }
        return $rowset;
    }

    public function saveCliente(Referido $cliente)
    {
        $data = array(
            'Nombres_Apellidos' => $cliente->Nombres_Apellidos,
            'Telefonos' => $cliente->Telefonos,
            'Fecha_referencia' => $cliente->Fecha_referencia,
            'cliente_id' => $cliente->cliente_id,
        );

        $id = (int)$cliente->id;

        if ($id == 0) {
            $data['Creacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        }
        return $id;
    }

    public function getAllClients($searchClient = null, $searchDateIni = null, $searchDateEnd = null, $order_by = "", $order = "")
    {
        $select = new Select();
        $select->from("BNF4_LandingReferidos");
        $select->columns(array("*"));
        $select->join(
            "BNF4_LandingClientesColaboradores",
            "BNF4_LandingClientesColaboradores.id = BNF4_LandingReferidos.cliente_id",
            array("ReferidoPor" => "Nombres_Apellidos")
        );

        if (!empty($searchClient)) {
            $select->where(
                "BNF4_LandingReferidos.Nombres_Apellidos LIKE '%" . $searchClient . "%'");
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF4_LandingReferidos.id DESC");
        }

        if ($searchDateIni != '' and $searchDateEnd != '') {
            $select->where->addPredicate(
                new Expression("date(BNF4_LandingReferidos.Fecha_referencia) BETWEEN '$searchDateIni' AND '$searchDateEnd'")
            );
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
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
}
