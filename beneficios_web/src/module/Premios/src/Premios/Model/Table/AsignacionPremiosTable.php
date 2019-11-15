<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:14 PM
 */

namespace Premios\Model\Table;

use Premios\Model\AsignacionPremios;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AsignacionPremiosTable
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

    public function getAsignacion($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getAsignacionesCliente($cliente)
    {
        $resultSet = $this->tableGateway->select(
            array('BNF_Cliente_id' => $cliente, 'EstadoPremios' => 'Activado')
        );
        return $resultSet;
    }

    public function updateAsignacion(AsignacionPremios $asignacion)
    {
        $data = array(
            'CantidadPremiosUsados' => $asignacion->CantidadPremiosUsados,
            'CantidadPremiosDisponibles' => $asignacion->CantidadPremiosDisponibles,
        );

        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => $asignacion->id));
    }

    public function getExistAssignedForUsuariosAndEmpresa($empresa, $cliente_id)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->and->equalTo("BNF_Cliente.id", $cliente_id);

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        if (!$resultSet->count()) {
            return false;
        }
        return true;
    }

    public function getAsignacionForCliente($cliente_id)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Cliente.id", $cliente_id);
        //$select->where->equalTo("BNF_Empresa.id", $empresa);
        //$select->where->equalTo("BNF3_Asignacion_Premios.EstadoPremios", 'Activado');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPremiosAsignados($cliente_id)
    {
        $select = new Select();
        $select->from('BNF3_Asignacion_Premios');
        $select->columns(
            array(
                'TotalAsignados' => new Expression('SUM(CantidadPremiosDisponibles)')
            )
        );
        $select->join(
            'BNF_Cliente',
            'BNF3_Asignacion_Premios.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Cliente.NumeroDocumento", $cliente_id)
            ->AND->equalTo("EstadoPremios", "Activado")
            ->AND->equalTo("BNF3_Asignacion_Premios.Eliminado", 0);

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}
