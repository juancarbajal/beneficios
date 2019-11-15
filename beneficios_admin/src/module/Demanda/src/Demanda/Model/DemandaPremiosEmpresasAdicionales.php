<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/06/16
 * Time: 12:33 AM
 */

namespace Demanda\Model;

class DemandaPremiosEmpresasAdicionales
{
    public $id;
    public $NombreEmpresa;
    public $BNF3_Demanda_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->NombreEmpresa = (!empty($data['NombreEmpresa'])) ? $data['NombreEmpresa'] : null;
        $this->BNF3_Demanda_id = (!empty($data['BNF3_Demanda_id'])) ? $data['BNF3_Demanda_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
