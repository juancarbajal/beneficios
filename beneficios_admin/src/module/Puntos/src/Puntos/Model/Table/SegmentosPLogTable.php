<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/08/16
 * Time: 11:52 AM
 */

namespace Puntos\Model\Table;

use Puntos\Model\SegmentosPLog;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class SegmentosPLogTable
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

    public function getSegmentosPLog($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveSegmentosPLog(SegmentosPLog $segmentosPLog)
    {
        $data = array(
            'BNF2_Segmentos_id' => $segmentosPLog->BNF2_Segmentos_id,
            'BNF2_Campania_id' => $segmentosPLog->BNF2_Campania_id,
            'NombreSegmento' => $segmentosPLog->NombreSegmento,
            'CantidadPuntos' => $segmentosPLog->CantidadPuntos,
            'CantidadPersonas' => $segmentosPLog->CantidadPersonas,
            'Subtotal' => $segmentosPLog->Subtotal,
            'Comentario' => $segmentosPLog->Comentario,
            'Eliminado' => $segmentosPLog->Eliminado,
            'RazonEliminado' => $segmentosPLog->RazonEliminado,
        );

        $id = (int)$segmentosPLog->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            throw new \Exception('SegmentosPLog id no create');
        }
    }
}
