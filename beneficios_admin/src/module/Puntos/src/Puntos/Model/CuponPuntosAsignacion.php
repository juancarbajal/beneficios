<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:07 PM
 */

namespace Puntos\Model;

class CuponPuntosAsignacion
{
    public $id;
    public $BNF2_Cupon_Puntos_id;
    public $BNF2_Asignacion_Puntos_id;
    public $PunosUtilizados;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Cupon_Puntos_id = (!empty($data['BNF2_Cupon_Puntos_id'])) ? $data['BNF2_Cupon_Puntos_id'] : null;
        $this->BNF2_Asignacion_Puntos_id = (!empty($data['BNF2_Asignacion_Puntos_id'])) ? $data['BNF2_Asignacion_Puntos_id'] : null;
        $this->PunosUtilizados = (!empty($data['PunosUtilizados'])) ? $data['PunosUtilizados'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
