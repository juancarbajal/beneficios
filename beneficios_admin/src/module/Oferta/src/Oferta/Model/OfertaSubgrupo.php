<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 27/09/15
 * Time: 08:48 PM
 */

namespace Oferta\Model;


class OfertaSubgrupo
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Subgrupo_id;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Subgrupo_id = (!empty($data['BNF_Subgrupo_id'])) ? $data['BNF_Subgrupo_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
