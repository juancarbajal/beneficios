<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/09/15
 * Time: 10:12 AM
 */

namespace Oferta\Model;


class OfertaRubro
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Rubro_id;
    public $Eliminado;
    public $Nombre;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Rubro_id = (!empty($data['BNF_Rubro_id'])) ? $data['BNF_Rubro_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
