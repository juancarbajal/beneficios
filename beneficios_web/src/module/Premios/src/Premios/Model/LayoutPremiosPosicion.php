<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:31 PM
 */

namespace Premios\Model;

class LayoutPremiosPosicion
{
    public $id;
    public $BNF_LayoutPremios_id;
    public $BNF3_Oferta_Premios_id;
    public $Index;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_LayoutPremios_id = (!empty($data['BNF_LayoutPremios_id'])) ? $data['BNF_LayoutPremios_id'] : null;
        $this->BNF3_Oferta_Premios_id = (!empty($data['BNF3_Oferta_Premios_id'])) ? $data['BNF3_Oferta_Premios_id'] : null;
        $this->Index = (!empty($data['Index'])) ? $data['Index'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}