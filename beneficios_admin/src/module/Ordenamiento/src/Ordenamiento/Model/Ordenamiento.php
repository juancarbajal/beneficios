<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/09/15
 * Time: 04:36 PM
 */

namespace Ordenamiento\Model;

class Ordenamiento
{
    public $id;
    public $Nombre;
    public $imagen;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id']))                 ? $data['id'] : null;
        $this->Nombre               = (!empty($data['Nombre']))             ? $data['Nombre'] : null;
        $this->imagen               = (!empty($data['imagen']))             ? $data['imagen'] : null;
        $this->FechaCreacion        = (!empty($data['FechaCreacion']))      ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion   = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado            = (!empty($data['Eliminado']))          ? $data['Eliminado'] : null;

        $this->NombreLayout         = (!empty($data['NombreLayout']))       ? $data['NombreLayout'] : null;
        $this->NombreTipo           = (!empty($data['NombreTipo']))         ? $data['NombreTipo'] : null;
        $this->Index                = (!empty($data['Index']))              ? $data['Index'] : null;
        $this->Tipo                 = (!empty($data['Tipo']))               ? $data['Tipo'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
