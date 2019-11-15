<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:12 PM
 */

namespace Premios\Model;

class OfertaPremiosRubro
{
    public $id;
    public $BNF3_Oferta_Premios_id;
    public $BNF_Rubro_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Oferta_Premios_id = (!empty($data['BNF3_Oferta_Premios_id'])) ? $data['BNF3_Oferta_Premios_id'] : null;
        $this->BNF_Rubro_id = (!empty($data['BNF_Rubro_id'])) ? $data['BNF_Rubro_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
