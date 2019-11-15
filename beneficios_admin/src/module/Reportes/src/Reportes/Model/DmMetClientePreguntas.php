<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 08/02/16
 * Time: 09:46 AM
 */

namespace Reportes\Model;

class DmMetClientePreguntas
{
    public $id;
    public $BNF_Cliente_id;
    public $BNF_DM_Dim_Empresa_id;
    public $BNF_DM_Dim_EstadoCivil_id;
    public $BNF_DM_Dim_Hijos_id;
    public $BNF_DM_Dim_Edad_id;
    public $Genero;
    public $nombres;
    public $apellidos;
    public $distrito_vive;
    public $distrito_trabaja;


    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_DM_Dim_Empresa_id = (!empty($data['BNF_DM_Dim_Empresa_id'])) ? $data['BNF_DM_Dim_Empresa_id'] : null;
        $this->BNF_DM_Dim_EstadoCivil_id = (!empty($data['BNF_DM_Dim_EstadoCivil_id']))
            ? $data['BNF_DM_Dim_EstadoCivil_id'] : null;
        $this->BNF_DM_Dim_Hijos_id = (!empty($data['BNF_DM_Dim_Hijos_id'])) ? $data['BNF_DM_Dim_Hijos_id'] : null;
        $this->BNF_DM_Dim_Edad_id = (!empty($data['BNF_DM_Dim_Edad_id'])) ? $data['BNF_DM_Dim_Edad_id'] : null;
        $this->Genero = (!empty($data['Genero'])) ? $data['Genero'] : null;
        $this->nombres = (!empty($data['nombres'])) ? $data['nombres'] : null;
        $this->apellidos = (!empty($data['apellidos'])) ? $data['apellidos'] : null;
        $this->distrito_vive = (!empty($data['distrito_vive'])) ? $data['distrito_vive'] : null;
        $this->distrito_trabaja = (!empty($data['distrito_trabaja'])) ? $data['distrito_trabaja'] : null;


        $this->Cantidad = (!empty($data['Cantidad'])) ? $data['Cantidad'] : null;
        $this->NoDef = (!empty($data['NoDef'])) ? $data['NoDef'] : null;
        $this->NoHijos = (!empty($data['NoHijos'])) ? $data['NoHijos'] : null;
        $this->SiHijos = (!empty($data['SiHijos'])) ? $data['SiHijos'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}

