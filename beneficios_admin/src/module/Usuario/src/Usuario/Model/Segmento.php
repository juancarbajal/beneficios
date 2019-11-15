<?php

namespace Usuario\Model;

class Segmento
{
    public $id;
    public $Nombre;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id       = (!empty($data['id']))     ? $data['id']     : null;
        $this->Nombre   = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
