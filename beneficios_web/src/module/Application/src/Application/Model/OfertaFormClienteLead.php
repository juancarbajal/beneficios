<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 07:25 PM
 */

namespace Application\Model;

class OfertaFormClienteLead
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Cliente_id;
    public $BNF_Empresa_id;
    public $BNF_Categoria_id;
    public $BNF_Formulario_id;
    public $Descripcion;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Categoria_id = (!empty($data['BNF_Categoria_id'])) ? $data['BNF_Categoria_id'] : null;
        $this->BNF_Formulario_id = (!empty($data['BNF_Formulario_id'])) ? $data['BNF_Formulario_id'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
