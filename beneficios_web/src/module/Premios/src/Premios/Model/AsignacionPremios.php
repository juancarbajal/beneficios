<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:07 PM
 */

namespace Premios\Model;

class AsignacionPremios
{
    public $id;
    public $BNF3_Segmento_id;
    public $BNF_Cliente_id;
    public $CantidadPremios;
    public $CantidadPremiosUsados;
    public $CantidadPremiosDisponibles;
    public $CantidadPremiosEliminados;
    public $EstadoPremios;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $TotalAsignados;
    public $TotalUsuarios;
    public $NumeroDocumento;
    public $Nombre;
    public $Apellido;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Segmento_id = (!empty($data['BNF3_Segmento_id'])) ? $data['BNF3_Segmento_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->CantidadPremios = (!empty($data['CantidadPremios'])) ? $data['CantidadPremios'] : 0;
        $this->CantidadPremiosUsados = (!empty($data['CantidadPremiosUsados'])) ? $data['CantidadPremiosUsados'] : 0;
        $this->CantidadPremiosDisponibles = (!empty($data['CantidadPremiosDisponibles'])) ? $data['CantidadPremiosDisponibles'] : 0;
        $this->CantidadPremiosEliminados = (!empty($data['CantidadPremiosEliminados'])) ? $data['CantidadPremiosEliminados'] : 0;
        $this->EstadoPremios = (!empty($data['EstadoPremios'])) ? $data['EstadoPremios'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->TotalAsignados = (!empty($data['TotalAsignados'])) ? $data['TotalAsignados'] : 0;
        $this->TotalUsuarios = (!empty($data['TotalUsuarios'])) ? $data['TotalUsuarios'] : 0;
        $this->NumeroDocumento = (!empty($data['NumeroDocumento'])) ? $data['NumeroDocumento'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Apellido = (!empty($data['Apellido'])) ? $data['Apellido'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
