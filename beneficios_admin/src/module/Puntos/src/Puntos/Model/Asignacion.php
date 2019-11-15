<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 05:07 PM
 */

namespace Puntos\Model;

class Asignacion
{
    public $id;
    public $BNF2_Segmento_id;
    public $BNF_Cliente_id;
    public $TipoAsignamiento;
    public $CantidadPuntos;
    public $CantidadPuntosUsados;
    public $CantidadPuntosDisponibles;
    public $CantidadPuntosEliminados;
    public $EstadoPuntos;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $TotalAsignados;
    public $TotalAplicados;
    public $TotalDisponibles;
    public $TotalUsuarios;
    public $NumeroDocumento;
    public $Nombre;
    public $Apellido;
    public $Empresa;
    public $Redimidos;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Segmento_id = (!empty($data['BNF2_Segmento_id'])) ? $data['BNF2_Segmento_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->TipoAsignamiento = (!empty($data['TipoAsignamiento'])) ? $data['TipoAsignamiento'] : 'Normal';
        $this->CantidadPuntos = (!empty($data['CantidadPuntos'])) ? $data['CantidadPuntos'] : null;
        $this->CantidadPuntosUsados = (!empty($data['CantidadPuntosUsados'])) ? $data['CantidadPuntosUsados'] : null;
        $this->CantidadPuntosDisponibles = (!empty($data['CantidadPuntosDisponibles']))
            ? $data['CantidadPuntosDisponibles'] : null;
        $this->CantidadPuntosEliminados = (!empty($data['CantidadPuntosEliminados']))
            ? $data['CantidadPuntosEliminados'] : null;
        $this->EstadoPuntos = (!empty($data['EstadoPuntos'])) ? $data['EstadoPuntos'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->TotalAsignados = (!empty($data['TotalAsignados'])) ? $data['TotalAsignados'] : 0;
        $this->TotalAplicados = (!empty($data['TotalAplicados'])) ? $data['TotalAplicados'] : 0;
        $this->TotalDisponibles = (!empty($data['TotalDisponibles'])) ? $data['TotalDisponibles'] : 0;
        $this->TotalUsuarios = (!empty($data['TotalUsuarios'])) ? $data['TotalUsuarios'] : 0;
        $this->NumeroDocumento = (!empty($data['NumeroDocumento'])) ? $data['NumeroDocumento'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Apellido = (!empty($data['Apellido'])) ? $data['Apellido'] : null;
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        $this->Redimidos = (!empty($data['Redimidos'])) ? $data['Redimidos'] : null;

        $this->UsuRedimidos = (!empty($data['UsuRedimidos'])) ? $data['UsuRedimidos'] : null;
        $this->UsuAplicados = (!empty($data['UsuAplicados'])) ? $data['UsuAplicados'] : null;
        $this->UsuAsignados = (!empty($data['UsuAsignados'])) ? $data['UsuAsignados'] : null;
        $this->Campania = (!empty($data['Campania'])) ? $data['Campania'] : null;
        $this->Segmento = (!empty($data['Segmento'])) ? $data['Segmento'] : null;
        $this->Rubro = (!empty($data['Rubro'])) ? $data['Rubro'] : null;

        $this->Correos = (!empty($data['Correos'])) ? $data['Correos'] : null;
        $this->Pregunta01 = (!empty($data['Pregunta01'])) ? $data['Pregunta01'] : null;
        $this->Pregunta02 = (!empty($data['Pregunta02'])) ? $data['Pregunta02'] : null;
        $this->Pregunta03 = (!empty($data['Pregunta03'])) ? $data['Pregunta03'] : null;
        $this->Pregunta04 = (!empty($data['Pregunta04'])) ? $data['Pregunta04'] : null;
        $this->Pregunta05 = (!empty($data['Pregunta05'])) ? $data['Pregunta05'] : null;
        $this->Pregunta06 = (!empty($data['Pregunta06'])) ? $data['Pregunta06'] : null;
        $this->Pregunta07 = (!empty($data['Pregunta07'])) ? $data['Pregunta07'] : null;
        $this->Pregunta08 = (!empty($data['Pregunta08'])) ? $data['Pregunta08'] : null;
        $this->Pregunta09 = (!empty($data['Pregunta09'])) ? $data['Pregunta09'] : null;
        $this->Pregunta10 = (!empty($data['Pregunta10'])) ? $data['Pregunta10'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
