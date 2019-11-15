<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/09/15
 * Time: 01:17 AM
 */

namespace Paquete\Model;

class Pais
{
    public $id;
    public $NombrePais;

    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id']))                  ? $data['id'] : null;
        $this->NombrePais           = (!empty($data['NombrePais']))          ? $data['NombrePais'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
