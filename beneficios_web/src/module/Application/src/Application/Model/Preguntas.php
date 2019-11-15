<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/12/15
 * Time: 01:01 PM
 */

namespace Application\Model;


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
    public $FechaPregunta01;
    public $FechaPregunta02;
    public $FechaPregunta03;
    public $FechaPregunta04;
    public $FechaPregunta05;
    public $FechaPregunta06;
    public $FechaPregunta07;
    public $FechaPregunta08;
    public $FechaPregunta09;
    public $FechaPregunta10;

    public $Nombre;
    public $Apellido;
    public $Correo;
    public $Telefono;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;

        $this->Pregunta01 = (isset($data['Pregunta01']) || @$data['Pregunta01'] != null) ? $data['Pregunta01'] : "";
        $this->Pregunta02 = (isset($data['Pregunta02']) || @$data['Pregunta02'] != null) ? $data['Pregunta02'] : "";
        $this->Pregunta03 = (isset($data['Pregunta03']) || @$data['Pregunta03'] != null) ? $data['Pregunta03'] : "";
        $this->Pregunta04 = (isset($data['Pregunta04']) || @$data['Pregunta04'] != null) ? $data['Pregunta04'] : "";
        $this->Pregunta05 = (isset($data['Pregunta05']) || @$data['Pregunta05'] != null) ? $data['Pregunta05'] : "";
        $this->Pregunta06 = (isset($data['Pregunta06']) || @$data['Pregunta06'] != null) ? $data['Pregunta06'] : "";
        $this->Pregunta07 = (isset($data['Pregunta07']) || @$data['Pregunta07'] != null) ? $data['Pregunta07'] : "";
        $this->Pregunta08 = (isset($data['Pregunta08']) || @$data['Pregunta08'] != null) ? $data['Pregunta08'] : "";
        $this->Pregunta09 = (isset($data['Pregunta09']) || @$data['Pregunta09'] != null) ? $data['Pregunta09'] : "";
        $this->Pregunta10 = (isset($data['Pregunta10']) || @$data['Pregunta10'] != null) ? $data['Pregunta10'] : "";

        $this->FechaPregunta01 = (isset($data['FechaPregunta01'])) ? $data['FechaPregunta01'] : null;
        $this->FechaPregunta02 = (isset($data['FechaPregunta02'])) ? $data['FechaPregunta02'] : null;
        $this->FechaPregunta03 = (isset($data['FechaPregunta03'])) ? $data['FechaPregunta03'] : null;
        $this->FechaPregunta04 = (isset($data['FechaPregunta04'])) ? $data['FechaPregunta04'] : null;
        $this->FechaPregunta05 = (isset($data['FechaPregunta05'])) ? $data['FechaPregunta05'] : null;
        $this->FechaPregunta06 = (isset($data['FechaPregunta06'])) ? $data['FechaPregunta06'] : null;
        $this->FechaPregunta07 = (isset($data['FechaPregunta07'])) ? $data['FechaPregunta07'] : null;
        $this->FechaPregunta08 = (isset($data['FechaPregunta08'])) ? $data['FechaPregunta08'] : null;
        $this->FechaPregunta09 = (isset($data['FechaPregunta09'])) ? $data['FechaPregunta09'] : null;
        $this->FechaPregunta10 = (isset($data['FechaPregunta10'])) ? $data['FechaPregunta10'] : null;

        $this->Nombre = (isset($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Apellido = (isset($data['Apellido'])) ? $data['Apellido'] : null;
        $this->Correo = (isset($data['Correo'])) ? $data['Correo'] : null;
        $this->Telefono = (isset($data['Telefono'])) ? $data['Telefono'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
