<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 17/11/15
 * Time: 05:05 PM
 */

namespace Cupon\Model\Table;

use Cupon\Model\Configuraciones;
use Zend\Db\TableGateway\TableGateway;

class ConfiguracionesTable
{
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(null);
        return $resultSet;
    }

    public function getConfigId($id)
    {
        $resultSet = $this->tableGateway->select(array('id' => $id));
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getConfig($campo)
    {
        $resultSet = $this->tableGateway->select(array('Campo' => $campo));
        $row = $resultSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCupon(Configuraciones $config)
    {
        $data = $config->getArrayCopy();

        $id = (int)$config->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getConfigId($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Configuración no existe.');
            }
        }
        return $id;
    }
}
