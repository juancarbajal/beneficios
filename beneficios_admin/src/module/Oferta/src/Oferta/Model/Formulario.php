<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/11/15
 * Time: 05:16 PM
 */

namespace Oferta\Model;

class Formulario
{
    public $id;
    public $Descripcion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
