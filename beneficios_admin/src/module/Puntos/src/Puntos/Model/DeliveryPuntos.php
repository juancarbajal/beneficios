<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:07 PM
 */

namespace Puntos\Model;

class DeliveryPuntos
{
    public $id;
    public $BNF2_Oferta_Puntos_id;
    public $Etiqueta_Campo;
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
        $this->BNF2_Oferta_Puntos_id = (!empty($data['BNF2_Oferta_Puntos_id'])) ? $data['BNF2_Oferta_Puntos_id'] : null;
        $this->Etiqueta_Campo = (!empty($data['Etiqueta_Campo'])) ? $data['Etiqueta_Campo'] : null;
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
