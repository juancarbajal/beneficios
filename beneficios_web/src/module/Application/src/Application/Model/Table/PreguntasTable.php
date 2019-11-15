<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/12/15
 * Time: 01:04 PM
 */

namespace Application\Model\Table;

use Application\Model\Preguntas;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class PreguntasTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getPreguntas($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Cliente_id' => $id));
        $row = $rowset->current();

        if (!$row) {
            false;
        }
        return $row;
    }

    public function saveRespuestas($idCliente, $data)
    {
        $id = (int)$idCliente;
        return $this->tableGateway->update($data, array('BNF_Cliente_id' => $id));
    }

    public function getPerfil($id)
    {
        $select = new Select();
        $select->from('BNF_Preguntas');
        $select->columns(array('Nombre' => 'Pregunta01', 'Apellido' => 'Pregunta02', 'Telefono' => 'Pregunta09'));
        $select->join(
            'BNF_Cliente',
            'BNF_Cliente.id = BNF_Preguntas.BNF_Cliente_id',
            array()
        );
        $select->join(
            'BNF_ClienteCorreo',
            'BNF_Cliente.id = BNF_ClienteCorreo.BNF_Cliente_id',
            array('Correo'),
            'left'
        );
        $select->where->equalTo('BNF_Cliente.id', $id);
        $select->order('BNF_ClienteCorreo.FechaActualizacion DESC');

        //echo str_replace('"', '',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}