<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:13 PM
 */

namespace Premios\Model;

class OfertaPremiosCampania
{
    public $id;
    public $BNF3_Oferta_Premios_id;
    public $BNF_CampaniaUbigeo_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Campania;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Oferta_Premios_id = (!empty($data['BNF3_Oferta_Premios_id'])) ? $data['BNF3_Oferta_Premios_id'] : null;
        $this->BNF_CampaniaUbigeo_id = (!empty($data['BNF_CampaniaUbigeo_id'])) ? $data['BNF_CampaniaUbigeo_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Campania = (!empty($data['Campania'])) ? $data['Campania'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
