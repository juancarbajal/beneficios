<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 19/04/16
 * Time: 04:50 PM
 */

namespace Application\Model;

class LayoutCategoriaPosicion
{
    public $id;
    public $BNF_LayoutCategoria_id;
    public $BNF_Oferta_id;
    public $Index;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_LayoutCategoria_id = (!empty($data['BNF_LayoutCategoria_id']))
            ? $data['BNF_LayoutCategoria_id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
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
