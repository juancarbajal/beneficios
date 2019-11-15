<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/12/15
 * Time: 05:06 PM
 */

namespace Auth\Model;


class EmpresaSubgrupo
{
    public $id;
    public $Nombre;
    public $BNF_Empresa_id;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }
}