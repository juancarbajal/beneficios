<?php

namespace Referido\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Referido
{
    public $id;
    public $Nombres_Apellidos;
    public $Telefonos;
    public $Fecha_referencia;
    public $cliente_id;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombres_Apellidos = (!empty($data['Nombres_Apellidos'])) ? $data['Nombres_Apellidos'] : null;
        $this->Telefonos = (!empty($data['Telefonos'])) ? $data['Telefonos'] : null;
        $this->Fecha_referencia = (!empty($data['Fecha_referencia'])) ? $data['Fecha_referencia'] : null;
        $this->cliente_id = (!empty($data['cliente_id'])) ? $data['cliente_id'] : null;

        $this->ReferidoPor = (!empty($data['ReferidoPor'])) ? $data['ReferidoPor'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
