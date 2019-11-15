<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/09/15
 * Time: 07:04 PM
 */

namespace Application\Model;


class OfertaUbigeo
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Ubigeo_id;
    public $Eliminado;
    public $Nombre;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Ubigeo_id = (!empty($data['BNF_Ubigeo_id'])) ? $data['BNF_Ubigeo_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
