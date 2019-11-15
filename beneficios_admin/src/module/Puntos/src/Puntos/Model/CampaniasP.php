<?php
/**
 * Created by PhpStorm.
 * User: janaq
 * Date: 17/06/16
 * Time: 12:29 PM
 */

namespace Puntos\Model;

class CampaniasP
{
    public $id;
    public $NombreCampania;
    public $TipoSegmento;
    public $FechaCampania;
    public $VigenciaInicio;
    public $VigenciaFin;
    public $PresupuestoNegociado;
    public $PresupuestoAsignado;
    public $ParametroAlerta;
    public $Comentario;
    public $Relacionado;
    public $EstadoCampania;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Presupuesto;
    public $Empresa;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->NombreCampania = (!empty($data['NombreCampania'])) ? $data['NombreCampania'] : null;
        $this->TipoSegmento = (!empty($data['TipoSegmento'])) ? $data['TipoSegmento'] : null;
        $this->FechaCampania = (!empty($data['FechaCampania'])) ? $data['FechaCampania'] : null;
        $this->VigenciaInicio = (!empty($data['VigenciaInicio'])) ? $data['VigenciaInicio'] : null;
        $this->VigenciaFin = (!empty($data['VigenciaFin'])) ? $data['VigenciaFin'] : null;
        $this->PresupuestoNegociado = (!empty($data['PresupuestoNegociado'])) ? $data['PresupuestoNegociado'] : null;
        $this->PresupuestoAsignado = (!empty($data['PresupuestoAsignado'])) ? $data['PresupuestoAsignado'] : null;
        $this->ParametroAlerta = (!empty($data['ParametroAlerta'])) ? $data['ParametroAlerta'] : null;
        $this->Comentario = (!empty($data['Comentario'])) ? $data['Comentario'] : null;
        $this->Relacionado = (!empty($data['Relacionado'])) ? $data['Relacionado'] : null;
        $this->EstadoCampania = (!empty($data['EstadoCampania'])) ? $data['EstadoCampania'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : 0;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Presupuesto = (!empty($data['Presupuesto'])) ? $data['Presupuesto'] : 0;
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
