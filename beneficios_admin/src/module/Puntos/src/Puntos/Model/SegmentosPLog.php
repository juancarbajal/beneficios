<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/08/16
 * Time: 11:36 AM
 */

namespace Puntos\Model;

class SegmentosPLog
{
    public $id;
    public $BNF2_Segmentos_id;
    public $BNF2_Campania_id;
    public $NombreSegmento;
    public $CantidadPuntos;
    public $CantidadPersonas;
    public $Subtotal;
    public $Comentario;
    public $Eliminado;
    public $RazonEliminado;
    public $FechaCreacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Segmentos_id = (!empty($data['BNF2_Segmentos_id'])) ? $data['BNF2_Segmentos_id'] : null;
        $this->BNF2_Campania_id = (!empty($data['BNF2_Campania_id'])) ? $data['BNF2_Campania_id'] : null;
        $this->NombreSegmento = (!empty($data['NombreSegmento'])) ? $data['NombreSegmento'] : null;
        $this->CantidadPuntos = (!empty($data['CantidadPuntos'])) ? $data['CantidadPuntos'] : null;
        $this->CantidadPersonas = (!empty($data['CantidadPersonas'])) ? $data['CantidadPersonas'] : null;
        $this->Subtotal = (!empty($data['Subtotal'])) ? $data['Subtotal'] : null;
        $this->Comentario = (!empty($data['Comentario'])) ? $data['Comentario'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->RazonEliminado = (!empty($data['RazonEliminado'])) ? $data['RazonEliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
