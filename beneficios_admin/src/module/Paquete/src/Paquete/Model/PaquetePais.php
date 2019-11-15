<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/09/15
 * Time: 11:26 AM
 */

namespace Paquete\Model;

class PaquetePais
{
    public $id;
    public $BNF_Paquete_id;
    public $BNF_Pais_id;

    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id']))                  ? $data['id'] : null;
        $this->BNF_Paquete_id       = (!empty($data['BNF_Paquete_id']))      ? $data['BNF_Paquete_id'] : null;
        $this->BNF_Pais_id          = (!empty($data['BNF_Pais_id']))         ? $data['BNF_Pais_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
