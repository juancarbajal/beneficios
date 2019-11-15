<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:13 PM
 */

namespace Puntos\Model;

class OfertaPuntosCategoria
{
    public $id;
    public $BNF2_Oferta_Puntos_id;
    public $BNF_CategoriaUbigeo_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Categoria;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Oferta_Puntos_id = (!empty($data['BNF2_Oferta_Puntos_id'])) ? $data['BNF2_Oferta_Puntos_id'] : null;
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
