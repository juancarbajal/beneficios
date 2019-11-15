<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/08/16
 * Time: 11:52 AM
 */

namespace Premios\Model\Table;

use Premios\Model\SegmentosPremiosLog;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class SegmentosPremiosLogTable
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

    public function getSegmentosPremiosLog($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveSegmentosPremiosLog(SegmentosPremiosLog $SegmentosPremiosLog)
    {
        $data = array(
            'BNF3_Segmentos_id' => $SegmentosPremiosLog->BNF3_Segmentos_id,
            'BNF3_Campania_id' => $SegmentosPremiosLog->BNF3_Campania_id,
            'NombreSegmento' => $SegmentosPremiosLog->NombreSegmento,
            'CantidadPremios' => $SegmentosPremiosLog->CantidadPremios,
            'CantidadPersonas' => $SegmentosPremiosLog->CantidadPersonas,
            'Subtotal' => $SegmentosPremiosLog->Subtotal,
            'Comentario' => $SegmentosPremiosLog->Comentario,
            'Eliminado' => $SegmentosPremiosLog->Eliminado,
            'RazonEliminado' => $SegmentosPremiosLog->RazonEliminado,
        );

        $id = (int)$SegmentosPremiosLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('SegmentosPremiosLog id no create');
        }
    }
}
