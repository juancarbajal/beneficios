<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/09/15
 * Time: 11:10 AM
 */

namespace Oferta\Model;


class OfertaCampaniaUbigeo
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_CampaniaUbigeo_id;
    public $Eliminado;
    public $Campania;
    public $Pais;
    public $Nombre;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_CampaniaUbigeo_id = (!empty($data['BNF_CampaniaUbigeo_id'])) ? $data['BNF_CampaniaUbigeo_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Campania = (!empty($data['Campania'])) ? $data['Campania'] : null;
        $this->Pais = (!empty($data['Pais'])) ? $data['Pais'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
