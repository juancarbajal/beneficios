<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:34 PM
 */

namespace Empresa\Model;


class EmpresaTipoEmpresa
{
    public $id;
    public $BNF_TipoEmpresa_id;
    public $BNF_Empresa_id;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_TipoEmpresa_id = (!empty($data['BNF_TipoEmpresa_id'])) ? $data['BNF_TipoEmpresa_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
