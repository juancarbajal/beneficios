<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 17/11/15
 * Time: 05:04 PM
 */

namespace Cupon\Model;


class Configuraciones
{
    public $id;
    public $Campo;
    public $Atributo;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Campo = (!empty($data['Campo'])) ? $data['Campo'] : null;
        $this->Atributo = (!empty($data['Atributo'])) ? $data['Atributo'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
