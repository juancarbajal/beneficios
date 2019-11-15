<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 02/09/15
 * Time: 07:19 PM
 */

namespace Cliente\Model;

class EmpresaSubgrupoCliente
{

    public $idBNF_EmpresaSubgrupoCliente;
    public $BNF_Subgrupo_id;
    public $BNF_Cliente_id;
    public $Eliminado;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->idBNF_EmpresaSubgrupoCliente = (!empty($data['idBNF_EmpresaSubgrupoCliente']))
            ? $data['idBNF_EmpresaSubgrupoCliente'] : null;
        $this->BNF_Subgrupo_id = (!empty($data['BNF_Subgrupo_id'])) ? $data['BNF_Subgrupo_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->NombreSubgrupo = (!empty($data['NombreSubgrupo'])) ? $data['NombreSubgrupo'] : null;
    }
}
