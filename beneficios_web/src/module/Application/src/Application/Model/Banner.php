<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/10/15
 * Time: 10:56 PM
 */

namespace Application\Model;


class Banner
{

    public $id;
    public $Nombre;
    public $Descripcion;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
