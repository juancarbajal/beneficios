<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 23/09/15
 * Time: 03:07 PM
 */
namespace Paquete\Model;

class BolsaTotal
{
    public $BNF_TipoPaquete_id;
    public $BNF_Empresa_id;
    public $BolsaActual;

    public function exchangeArray($data)
    {
        $this->BNF_TipoPaquete_id   = (!empty($data['BNF_TipoPaquete_id']))     ? $data['BNF_TipoPaquete_id'] : null;
        $this->BNF_Empresa_id       = (!empty($data['BNF_Empresa_id']))         ? $data['BNF_Empresa_id'] : null;
        $this->BolsaActual          = (!empty($data['BolsaActual']))            ? (int)$data['BolsaActual'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
