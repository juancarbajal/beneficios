<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 07:24 PM
 */

namespace Application\Model;

class FormularioLead
{
    public $id;
    public $BNF_Oferta_id;
    public $Nombre_Campo;
    public $Tipo_Campo;
    public $Detalle;
    public $Requerido;
    public $Activo;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->Nombre_Campo = (!empty($data['Nombre_Campo'])) ? $data['Nombre_Campo'] : null;
        $this->Tipo_Campo = (!empty($data['Tipo_Campo'])) ? $data['Tipo_Campo'] : null;
        $this->Detalle = (!empty($data['Detalle'])) ? $data['Detalle'] : null;
        $this->Requerido = (!empty($data['Requerido'])) ? $data['Requerido'] : null;
        $this->Activo = (!empty($data['Activo'])) ? $data['Activo'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
