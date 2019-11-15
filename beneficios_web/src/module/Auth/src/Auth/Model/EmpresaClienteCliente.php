<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/10/15
 * Time: 05:09 PM
 */

namespace Auth\Model;


class EmpresaClienteCliente
{
    public $id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $Estado;
    public $Eliminado;
    public $Beneficiario;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->Estado = (!empty($data['Estado'])) ? $data['Estado'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Beneficiario = (!empty($data['Beneficiario'])) ? $data['Beneficiario'] : null;
    }
}
