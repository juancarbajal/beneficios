<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 10/09/15
 * Time: 05:48 PM
 */

namespace Categoria\Model;

class CategoriaUbigeo
{
    public $id;
    public $BNF_Categoria_id;
    public $BNF_Pais_id;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id']))                 ? $data['id'] : null;
        $this->BNF_Categoria_id     = (!empty($data['BNF_Categoria_id']))   ? $data['BNF_Categoria_id'] : null;
        $this->BNF_Pais_id          = (!empty($data['BNF_Pais_id']))        ? $data['BNF_Pais_id'] : null;
        $this->Eliminado            = (!empty($data['Eliminado']))          ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
