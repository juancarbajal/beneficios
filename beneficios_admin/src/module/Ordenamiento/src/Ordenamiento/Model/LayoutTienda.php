<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 28/10/15
 * Time: 01:01 PM
 */

namespace Ordenamiento\Model;

class LayoutTienda
{
    public $id;
    public $BNF_Layout_id;
    public $BNF_Empresa_id;
    public $Index;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Layout_id = (!empty($data['BNF_Layout_id'])) ? $data['BNF_Layout_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Index = (!empty($data['Index'])) ? $data['Index'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
