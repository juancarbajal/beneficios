<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:07 PM
 */

namespace Application\Model;

class OfertaPuntosDelivery
{
    public $id;
    public $BNF2_Delivery_Puntos_id;
    public $BNF2_Oferta_Puntos_id;
    public $BNF2_Asignacion_Puntos_id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $Detalle;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Delivery_Puntos_id = (!empty($data['BNF2_Delivery_Puntos_id'])) ? $data['BNF2_Delivery_Puntos_id'] : null;
        $this->BNF2_Oferta_Puntos_id = (!empty($data['BNF2_Oferta_Puntos_id'])) ? $data['BNF2_Oferta_Puntos_id'] : null;
        $this->BNF2_Asignacion_Puntos_id = (!empty($data['BNF2_Asignacion_Puntos_id'])) ? $data['BNF2_Asignacion_Puntos_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->Detalle = (!empty($data['Detalle'])) ? $data['Detalle'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
