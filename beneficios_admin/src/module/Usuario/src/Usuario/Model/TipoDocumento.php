<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 31/08/15
 * Time: 10:54 AM
 */

namespace Usuario\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TipoDocumento
{
    public $id;
    public $Nombre;
    public $Eliminado;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
