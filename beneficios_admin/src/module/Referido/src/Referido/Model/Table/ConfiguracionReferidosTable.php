<?php

namespace Referido\Model\Table;

use Referido\Model\ConfiguracionReferidos;
use Zend\Db\TableGateway\TableGateway;

class ConfiguracionReferidosTable
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

    public function getConfiguracionReferidos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not fin row $id");
        }
        return $row;
    }

    public function getConfiguracionReferidosByTipo($tipo)
    {
        $rowset = $this->tableGateway->select(array('Tipo' => $tipo));
        $row = $rowset;

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getConfiguracionReferidosByCampo($campo)
    {
        $rowset = $this->tableGateway->select(array('Campo' => $campo));
        $row = $rowset->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveConfiguracionReferidos(ConfiguracionReferidos $configuracionReferidos)
    {
        $data = array(
            'Campo' => $configuracionReferidos->Campo,
            'Atributo' => $configuracionReferidos->Atributo,
            'Tipo' => $configuracionReferidos->Tipo,
            'Eliminado' => $configuracionReferidos->Eliminado,
        );

        $id = (int)$configuracionReferidos->id;

        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getConfiguracionReferidos($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Cliente id does not exist');
            }
        }
        return $id;
    }
}
