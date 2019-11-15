<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/09/15
 * Time: 12:34 AM
 */
namespace Paquete\Model;

class TipoPaquete
{
    public $id;
    public $NombreTipoPaquete;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id']))                  ? $data['id'] : null;
        $this->NombreTipoPaquete    = (!empty($data['NombreTipoPaquete']))   ? $data['NombreTipoPaquete'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
