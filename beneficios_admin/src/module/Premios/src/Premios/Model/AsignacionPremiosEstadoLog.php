<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/08/16
 * Time: 11:36 AM
 */

namespace Premios\Model;

class AsignacionPremiosEstadoLog
{
    public $id;
    public $BNF3_Asignacion_Premios_id;
    public $BNF3_Segmento_id;
    public $BNF_Cliente_id;
    public $CantidadPremios;
    public $CantidadPremiosUsados;
    public $CantidadPremiosDisponibles;
    public $CantidadPremiosEliminados;
    public $EstadoPremios;
    public $Operacion;
    public $Premios;
    public $Motivo;
    public $BNF_Usuario_id;
    public $FechaCreacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Segmento_id = (!empty($data['BNF3_Segmento_id'])) ? $data['BNF3_Segmento_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->CantidadPremios = (!empty($data['CantidadPremios'])) ? $data['CantidadPremios'] : null;
        $this->CantidadPremiosUsados = (!empty($data['CantidadPremiosUsados'])) ? $data['CantidadPremiosUsados'] : null;
        $this->CantidadPremiosDisponibles = (!empty($data['CantidadPremiosDisponibles']))
            ? $data['CantidadPremiosDisponibles'] : null;
        $this->CantidadPremiosEliminados = (!empty($data['CantidadPremiosEliminados']))
            ? $data['CantidadPremiosEliminados'] : null;
        $this->EstadoPremios = (!empty($data['EstadoPremios'])) ? $data['EstadoPremios'] : null;
        $this->Operacion = (!empty($data['Operacion'])) ? $data['Operacion'] : null;
        $this->Premios = (!empty($data['Premios'])) ? $data['Premios'] : null;
        $this->Motivo = (!empty($data['Motivo'])) ? $data['Motivo'] : null;
        $this->BNF_Usuario_id = (!empty($data['BNF_Usuario_id'])) ? $data['BNF_Usuario_id'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
