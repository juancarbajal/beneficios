<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/09/15
 * Time: 10:27 AM
 */

namespace Oferta\Model;


class OfertaCategoriaUbigeo
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_CategoriaUbigeo_id;
    public $Eliminado;
    public $Categoria;
    public $Pais;
    public $Nombre;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_CategoriaUbigeo_id = (!empty($data['BNF_CategoriaUbigeo_id']))
            ? $data['BNF_CategoriaUbigeo_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Categoria = (!empty($data['Categoria'])) ? $data['Categoria'] : null;
        $this->Pais = (!empty($data['Pais'])) ? $data['Pais'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
