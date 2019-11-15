<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 30/10/15
 * Time: 21:43
 */

namespace Application\Model;


class Configuraciones
{
    public $id;
    public $Campo;
    public $Atributo;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Campo = (!empty($data['Campo'])) ? $data['Campo'] : null;
        $this->Atributo = (!empty($data['Atributo'])) ? $data['Atributo'] : null;
    }
}