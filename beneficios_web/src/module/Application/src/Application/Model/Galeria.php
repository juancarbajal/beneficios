<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/10/15
 * Time: 10:56 PM
 */

namespace Application\Model;


class Galeria
{
    public $id;
    public $Imagen;
    public $Url;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Imagen = (!empty($data['Imagen'])) ? $data['Imagen'] : null;
        $this->Url = (!empty($data['Url'])) ? $data['Url'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
