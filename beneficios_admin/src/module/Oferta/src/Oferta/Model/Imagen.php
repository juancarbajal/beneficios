<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/09/15
 * Time: 05:45 PM
 */

namespace Oferta\Model;


class Imagen
{
    public $id;
    public $BNF_Oferta_id;
    public $Nombre;
    public $Eliminado;
    public $Principal;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Principal = (!empty($data['Principal'])) ? $data['Principal'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
