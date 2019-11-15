<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/08/16
 * Time: 04:30 PM
 */

namespace Application\Model;

class LayoutPuntos
{
    public $id;
    public $BNF_Layout_id;
    public $BNF_Empresa_id;
    public $Index;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public $TipoLayout;
    public $fila;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Layout_id = (!empty($data['BNF_Layout_id'])) ? $data['BNF_Layout_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Index = (!empty($data['Index'])) ? $data['Index'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;

        $this->TipoLayout = (!empty($data['TipoLayout'])) ? $data['TipoLayout'] : null;
        $this->fila = (!empty($data['fila'])) ? $data['fila'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}