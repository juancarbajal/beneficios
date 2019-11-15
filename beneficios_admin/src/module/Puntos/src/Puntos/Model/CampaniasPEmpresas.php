<?php
/**
 * Created by PhpStorm.
 * User: janaq
 * Date: 17/06/16
 * Time: 12:39 PM
 */

namespace Puntos\Model;

class CampaniasPEmpresas
{
    public $id;
    public $BNF2_Campania_id;
    public $BNF_Empresa_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Campania_id = (!empty($data['BNF2_Campania_id'])) ? $data['BNF2_Campania_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : 0;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}