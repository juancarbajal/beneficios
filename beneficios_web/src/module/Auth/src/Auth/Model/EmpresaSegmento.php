<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/12/15
 * Time: 04:48 PM
 */

namespace Auth\Model;


class EmpresaSegmento
{
    public $id;
    public $BNF_Empresa_id;
    public $BNF_Segmento_id;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Segmento_id = (!empty($data['BNF_Segmento_id'])) ? $data['BNF_Segmento_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }
}