<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:14 PM
 */

namespace Application\Model\Table;

use Application\Model\Asignacion;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class AsignacionTable
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
            array('BNF_Cliente_id' => $cliente, 'EstadoPuntos' => 'Activado')
        );
        return $resultSet;
    }

    public function updateAsignacion(Asignacion $asignacion)
    {
        $data = array(
            'CantidadPuntosUsados' => $asignacion->CantidadPuntosUsados,
            'CantidadPuntosDisponibles' => $asignacion->CantidadPuntosDisponibles,
        );

        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => $asignacion->id));
    }

    public function getExistAssignedForUsuariosAndEmpresa($empresa, $cliente_id)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
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
        $select->from('BNF2_Asignacion_Puntos');
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Cliente.id", $cliente_id);
        //$select->where->equalTo("BNF_Empresa.id", $empresa);
        //$select->where->equalTo("BNF2_Asignacion_Puntos.EstadoPuntos", 'Activado');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPuntosAsignados($cliente_id)
    {
        $select = new Select();
        $select->from('BNF2_Asignacion_Puntos');
        $select->columns(
            array(
                'TotalAsignados' => new Expression('SUM(CantidadPuntosDisponibles)')
            )
        );
        $select->join(
            'BNF_Cliente',
            'BNF2_Asignacion_Puntos.BNF_Cliente_id = BNF_Cliente.id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Cliente.NumeroDocumento", $cliente_id)
            ->AND->equalTo("EstadoPuntos", "Activado")
            ->AND->equalTo("BNF2_Asignacion_Puntos.Eliminado", 0);

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}
