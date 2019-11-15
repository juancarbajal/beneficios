<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/08/16
 * Time: 11:36 AM
 */

namespace Application\Model;

class AsignacionEstadoLog
{
    public $id;
    public $BNF2_Asignacion_Puntos_id;
    public $BNF2_Segmento_id;
    public $BNF_Cliente_id;
    public $CantidadPuntos;
    public $CantidadPuntosUsados;
    public $CantidadPuntosDisponibles;
    public $CantidadPuntosEliminados;
    public $EstadoPuntos;
    public $Operacion;
    public $Puntos;
    public $Motivo;
    public $BNF_Usuario_id;
    public $FechaCreacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Segmento_id = (!empty($data['BNF2_Segmento_id'])) ? $data['BNF2_Segmento_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->CantidadPuntos = (!empty($data['CantidadPuntos'])) ? $data['CantidadPuntos'] : null;
        $this->CantidadPuntosUsados = (!empty($data['CantidadPuntosUsados'])) ? $data['CantidadPuntosUsados'] : null;
        $this->CantidadPuntosDisponibles = (!empty($data['CantidadPuntosDisponibles']))
            ? $data['CantidadPuntosDisponibles'] : null;
        $this->CantidadPuntosEliminados = (!empty($data['CantidadPuntosEliminados']))
            ? $data['CantidadPuntosEliminados'] : null;
        $this->EstadoPuntos = (!empty($data['EstadoPuntos'])) ? $data['EstadoPuntos'] : null;
        $this->Operacion = (!empty($data['Operacion'])) ? $data['Operacion'] : null;
        $this->Puntos = (!empty($data['Puntos'])) ? $data['Puntos'] : null;
        $this->Motivo = (!empty($data['Motivo'])) ? $data['Motivo'] : null;
        $this->BNF_Usuario_id = (!empty($data['BNF_Usuario_id'])) ? $data['BNF_Usuario_id'] : null;
        $this->BNF_Usuario_id = (!empty($data['BNF_Usuario_id'])) ? $data['BNF_Usuario_id'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
