<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/08/16
 * Time: 11:36 AM
 */

namespace Premios\Model;

class SegmentosPremiosLog
{
    public $id;
    public $BNF3_Segmentos_id;
    public $BNF3_Campania_id;
    public $NombreSegmento;
    public $CantidadPremios;
    public $CantidadPersonas;
    public $Subtotal;
    public $Comentario;
    public $Eliminado;
    public $RazonEliminado;
    public $FechaCreacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Segmentos_id = (!empty($data['BNF3_Segmentos_id'])) ? $data['BNF3_Segmentos_id'] : null;
        $this->BNF3_Campania_id = (!empty($data['BNF3_Campania_id'])) ? $data['BNF3_Campania_id'] : null;
        $this->NombreSegmento = (!empty($data['NombreSegmento'])) ? $data['NombreSegmento'] : null;
        $this->CantidadPremios = (!empty($data['CantidadPremios'])) ? $data['CantidadPremios'] : null;
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
