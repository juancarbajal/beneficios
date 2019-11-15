<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 06:58 PM
 */

namespace Campania\Model;

class Campania
{
    public $id;
    public $Nombre;
    public $Descripcion;
    public $Slug;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;
    public $NombrePais;
    public $CU_id;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->Slug = (!empty($data['Slug'])) ? $data['Slug'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->NombrePais = (!empty($data['NombrePais'])) ? $data['NombrePais'] : null;
        $this->CU_id = (!empty($data['CU_id'])) ? $data['CU_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
