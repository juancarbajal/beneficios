<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/08/16
 * Time: 11:36 AM
 */

namespace Premios\Model;

class CampaniaPremiosLog
{
    public $id;
    public $BNF3_Campania_id;
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
    public $BNF_Empresa_id;
    public $Segmentos;
    public $RazonEliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Campania_id = (!empty($data['BNF3_Campania_id'])) ? $data['BNF3_Campania_id'] : null;
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
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Segmentos = (!empty($data['Segmentos'])) ? $data['Segmentos'] : null;
        $this->RazonEliminado = (!empty($data['RazonEliminado'])) ? $data['RazonEliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
