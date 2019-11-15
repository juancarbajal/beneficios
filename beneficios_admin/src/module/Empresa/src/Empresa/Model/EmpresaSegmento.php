<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:51 PM
 */

namespace Empresa\Model;

class EmpresaSegmento
{
    public $id;
    public $BNF_Empresa_id;
    public $BNF_Segmento_id;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Segmento_id = (!empty($data['BNF_Segmento_id'])) ? $data['BNF_Segmento_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->NombreSegmento = (!empty($data['NombreSegmento'])) ? $data['NombreSegmento'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
