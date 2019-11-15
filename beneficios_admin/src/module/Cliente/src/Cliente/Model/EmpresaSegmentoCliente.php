<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 02/09/15
 * Time: 07:18 PM
 */

namespace Cliente\Model;

class EmpresaSegmentoCliente
{
    public $idBNF_EmpresaSegmentoCliente;
    public $BNF_EmpresaSegmento_id;
    public $BNF_Cliente_id;
    public $Eliminado;

    //protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->idBNF_EmpresaSegmentoCliente = (!empty($data['idBNF_EmpresaSegmentoCliente']))
            ? $data['idBNF_EmpresaSegmentoCliente'] : null;
        $this->BNF_EmpresaSegmento_id = (!empty($data['BNF_EmpresaSegmento_id']))
            ? $data['BNF_EmpresaSegmento_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }
}
