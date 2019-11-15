<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 01/10/15
 * Time: 05:21 PM
 */

namespace Ordenamiento\Model;

class LayoutCampania
{
    public $id;
    public $BNF_Campanias_id;
    public $BNF_Layout_id;
    public $BNF_Empresa_id;
    public $Index;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id']))                 ? $data['id'] : null;
        $this->BNF_Campanias_id     = (!empty($data['BNF_Campanias_id']))   ? $data['BNF_Campanias_id'] : null;
        $this->BNF_Layout_id        = (!empty($data['BNF_Layout_id']))      ? $data['BNF_Layout_id'] : null;
        $this->BNF_Empresa_id       = (!empty($data['BNF_Empresa_id']))      ? $data['BNF_Empresa_id'] : null;
        $this->Index                = (!empty($data['Index']))              ? $data['Index'] : null;
        $this->FechaCreacion        = (!empty($data['FechaCreacion']))      ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion   = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado            = (!empty($data['Eliminado']))          ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
