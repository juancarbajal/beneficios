<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/10/15
 * Time: 03:06 PM
 */

namespace Oferta\Model;

class Busqueda
{
    public $id;
    public $BNF_Oferta_id;
    public $TipoOferta;
    public $Descripcion;
    public $Empresa;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->TipoOferta = (!empty($data['TipoOferta'])) ? $data['TipoOferta'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
