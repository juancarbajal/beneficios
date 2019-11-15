<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 11:58 PM
 */

namespace Campania\Model;

class CampaniaUbigeo
{
    public $id;
    public $BNF_Campanias_id;
    public $BNF_Pais_id;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Campanias_id = (!empty($data['BNF_Campanias_id'])) ? $data['BNF_Campanias_id'] : null;
        $this->BNF_Pais_id = (!empty($data['BNF_Pais_id'])) ? $data['BNF_Pais_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
