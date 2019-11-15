<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:07 PM
 */

namespace Application\Model;

class Asignacion
{
    public $id;
    public $BNF2_Segmento_id;
    public $BNF_Cliente_id;
    public $CantidadPuntos;
    public $CantidadPuntosUsados;
    public $CantidadPuntosDisponibles;
    public $CantidadPuntosEliminados;
    public $EstadoPuntos;
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
        $this->BNF2_Segmento_id = (!empty($data['BNF2_Segmento_id'])) ? $data['BNF2_Segmento_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->CantidadPuntos = (!empty($data['CantidadPuntos'])) ? $data['CantidadPuntos'] : 0;
        $this->CantidadPuntosUsados = (!empty($data['CantidadPuntosUsados'])) ? $data['CantidadPuntosUsados'] : 0;
        $this->CantidadPuntosDisponibles = (!empty($data['CantidadPuntosDisponibles'])) ? $data['CantidadPuntosDisponibles'] : 0;
        $this->CantidadPuntosEliminados = (!empty($data['CantidadPuntosEliminados'])) ? $data['CantidadPuntosEliminados'] : 0;
        $this->EstadoPuntos = (!empty($data['EstadoPuntos'])) ? $data['EstadoPuntos'] : null;
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
