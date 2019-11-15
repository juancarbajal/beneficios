<?php

namespace Referido\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ConfiguracionReferidos
{
    public $id;
    public $Campo;
    public $Atributo;
    public $Tipo;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Campo = (!empty($data['Campo'])) ? $data['Campo'] : null;
        $this->Atributo = (!empty($data['Atributo'])) ? $data['Atributo'] : null;
        $this->Tipo = (!empty($data['Tipo'])) ? $data['Tipo'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
