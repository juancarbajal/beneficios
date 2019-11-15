<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:12 PM
 */

namespace Puntos\Model;

class OfertaPuntosSegmento
{
    public $id;
    public $BNF2_Oferta_Puntos_id;
    public $BNF2_Segmento_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Oferta_Puntos_id = (!empty($data['BNF2_Oferta_Puntos_id'])) ? $data['BNF2_Oferta_Puntos_id'] : null;
        $this->BNF2_Segmento_id = (!empty($data['BNF2_Segmento_id'])) ? $data['BNF2_Segmento_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
