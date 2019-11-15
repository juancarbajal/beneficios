<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 31/08/15
 * Time: 12:58 AM
 */

namespace Usuario\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TipoUsuario
{
    public $id;
    public $Nombre;
    public $Descripcion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre  = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Descripcion  = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
