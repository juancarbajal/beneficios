<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 04/09/15
 * Time: 03:50 PM
 */

namespace Empresa\Model;

class Ubigeo
{
    public $id;
    public $Nombre;
    public $id_padre;
    public $BNF_Pais_id;

    public $Pais;
    public $Departamento;
    public $Provincia;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->id_padre = (!empty($data['id_padre'])) ? $data['id_padre'] : null;
        $this->BNF_Pais_id = (!empty($data['BNF_Pais_id'])) ? $data['BNF_Pais_id'] : null;

        $this->Pais = (!empty($data['Pais'])) ? $data['Pais'] : null;
        $this->Departamento = (!empty($data['Departamento'])) ? $data['Departamento'] : null;
        $this->Provincia = (!empty($data['Provincia'])) ? $data['Provincia'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
