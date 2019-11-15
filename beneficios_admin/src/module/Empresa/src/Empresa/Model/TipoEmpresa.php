<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:27 PM
 */

namespace Empresa\Model;


class TipoEmpresa
{
    public $id;
    public $Nombre;
    public $Descripcion;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre       = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Descripcion  = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
