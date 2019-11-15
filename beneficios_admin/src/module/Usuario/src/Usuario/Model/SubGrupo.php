<?php

namespace Usuario\Model;

class SubGrupo
{
    public $id;
    public $Nombre;
    public $BNF_Empresa_id;
    public $Eliminado;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id               = (!empty($data['id']))             ? $data['id']               : null;
        $this->Nombre           = (!empty($data['Nombre']))         ? $data['Nombre']           : null;
        $this->BNF_Empresa_id   = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id']   : null;
        $this->Eliminado        = (!empty($data['Eliminado']))      ? $data['Eliminado']        : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
