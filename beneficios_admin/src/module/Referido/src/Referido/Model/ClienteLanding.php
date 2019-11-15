<?php

namespace Referido\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ClienteLanding
{
    public $id;
    public $Nombres_Apellidos;
    public $Telefonos;
    public $Email;
    public $Especialista;
    public $Creado;
    public $Documento;
    public $Tipo;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombres_Apellidos = (!empty($data['Nombres_Apellidos'])) ? $data['Nombres_Apellidos'] : null;
        $this->Telefonos = (!empty($data['Telefonos'])) ? $data['Telefonos'] : null;
        $this->Email = (!empty($data['Email'])) ? $data['Email'] : null;
        $this->Especialista = (!empty($data['Especialista'])) ? $data['Especialista'] : null;
        $this->Creado = (!empty($data['Creado'])) ? $data['Creado'] : null;
        $this->Documento = (!empty($data['Documento'])) ? $data['Documento'] : null;
        $this->Tipo = (!empty($data['Tipo'])) ? $data['Tipo'] : null;

        $this->FechaAsignacion = (!empty($data['FechaAsignacion'])) ? $data['FechaAsignacion'] : null;
        $this->PuntosAsignados = (!empty($data['PuntosAsignados'])) ? $data['PuntosAsignados'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
