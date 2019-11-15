<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 22/09/15
 * Time: 11:21 AM
 */

namespace Oferta\Model;


class TipoBeneficio
{
    public $id;
    public $NombreBeneficio;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->NombreBeneficio = (!empty($data['NombreBeneficio'])) ? $data['NombreBeneficio'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
