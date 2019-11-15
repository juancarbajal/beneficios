<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/10/15
 * Time: 10:27 PM
 */

namespace Ordenamiento\Model;

class Galeria
{
    public $id;
    public $Imagen;
    public $Url;
    public $FechaSubida;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Imagen = (!empty($data['Imagen'])) ? $data['Imagen'] : null;
        $this->Url = (!empty($data['Url'])) ? $data['Url'] : null;
        $this->FechaSubida = (!empty($data['FechaSubida'])) ? $data['FechaSubida'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
