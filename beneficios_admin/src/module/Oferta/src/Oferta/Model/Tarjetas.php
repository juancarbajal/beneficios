<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 11/04/16
 * Time: 05:45 PM
 */

namespace Oferta\Model;

class Tarjetas
{
    public $id;
    public $Descripcion;
    public $Imagen;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->Imagen = (!empty($data['Imagen'])) ? $data['Imagen'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
