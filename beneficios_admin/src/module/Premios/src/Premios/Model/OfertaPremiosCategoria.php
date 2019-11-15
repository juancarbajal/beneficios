<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:13 PM
 */

namespace Premios\Model;

class OfertaPremiosCategoria
{
    public $id;
    public $BNF3_Oferta_Premios_id;
    public $BNF_CategoriaUbigeo_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Categoria;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Oferta_Premios_id = (!empty($data['BNF3_Oferta_Premios_id'])) ? $data['BNF3_Oferta_Premios_id'] : null;
        $this->BNF_CategoriaUbigeo_id = (!empty($data['BNF_CategoriaUbigeo_id'])) ? $data['BNF_CategoriaUbigeo_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->Categoria = (!empty($data['Categoria'])) ? $data['Categoria'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
