<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/08/16
 * Time: 05:50 PM
 */

namespace Application\Model;

class CuponPuntosLog
{
    public $id;
    public $BNF2_Cupon_Puntos_id;
    public $CodigoCupon;
    public $EstadoCupon;
    public $BNF2_Oferta_Puntos_id;
    public $BNF2_Oferta_Puntos_Atributos_id;
    public $BNF_Cliente_id;
    public $BNF_Usuario_id;
    public $Comentario;
    public $FechaCreacion;


    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Cupon_Puntos_id = (!empty($data['BNF2_Cupon_Puntos_id'])) ? $data['BNF2_Cupon_Puntos_id'] : null;
        $this->CodigoCupon = (!empty($data['CodigoCupon'])) ? $data['CodigoCupon'] : null;
        $this->EstadoCupon = (!empty($data['EstadoCupon'])) ? $data['EstadoCupon'] : null;
        $this->BNF2_Oferta_Puntos_id = (!empty($data['BNF2_Oferta_Puntos_id'])) ? $data['BNF2_Oferta_Puntos_id'] : null;
        $this->BNF2_Oferta_Puntos_Atributos_id =
            (!empty($data['BNF2_Oferta_Puntos_Atributos_id'])) ? $data['BNF2_Oferta_Puntos_Atributos_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_Usuario_id = (!empty($data['BNF_Usuario_id'])) ? $data['BNF_Usuario_id'] : null;
        $this->Comentario = (!empty($data['Comentario'])) ? $data['Comentario'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
