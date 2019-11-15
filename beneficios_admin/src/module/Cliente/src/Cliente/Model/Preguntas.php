<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/12/15
 * Time: 06:10 PM
 */

namespace Cliente\Model;


class Preguntas
{
    public $id;
    public $BNF_Cliente_id;
    public $Pregunta01;
    public $Pregunta02;
    public $Pregunta03;
    public $Pregunta04;
    public $Pregunta05;
    public $Pregunta06;
    public $Pregunta07;
    public $Pregunta08;
    public $Pregunta09;
    public $Pregunta10;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->Pregunta01 = (!empty($data['Pregunta01'])) ? $data['Pregunta01'] : null;
        $this->Pregunta02 = (!empty($data['Pregunta02'])) ? $data['Pregunta02'] : null;
        $this->Pregunta03 = (!empty($data['Pregunta03'])) ? $data['Pregunta03'] : null;
        $this->Pregunta04 = (!empty($data['Pregunta04'])) ? $data['Pregunta04'] : null;
        $this->Pregunta05 = (!empty($data['Pregunta05'])) ? $data['Pregunta05'] : null;
        $this->Pregunta06 = (!empty($data['Pregunta06'])) ? $data['Pregunta06'] : null;
        $this->Pregunta07 = (!empty($data['Pregunta07'])) ? $data['Pregunta07'] : null;
        $this->Pregunta08 = (!empty($data['Pregunta08'])) ? $data['Pregunta08'] : null;
        $this->Pregunta09 = (!empty($data['Pregunta09'])) ? $data['Pregunta09'] : null;
        $this->Pregunta10 = (!empty($data['Pregunta10'])) ? $data['Pregunta10'] : null;
        $this->Cantidad = (!empty($data['Cantidad'])) ? $data['Cantidad'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}