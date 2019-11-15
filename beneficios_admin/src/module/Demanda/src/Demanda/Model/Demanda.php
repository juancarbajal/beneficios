<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/06/16
 * Time: 05:54 PM
 */

namespace Demanda\Model;

class Demanda
{
    public $id;
    public $BNF_Empresa_id;
    public $FechaDemanda;
    public $PrecioMinimo;
    public $PrecioMaximo;
    public $Target;
    public $Comentarios;
    public $Actualizaciones;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    //
    public $Empresa;
    public $Ruc;
    public $CorreoPersonaAtencion;
    public $Campania;
    public $Rubro;
    public $EmpresasAdicionales;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->FechaDemanda = (!empty($data['FechaDemanda'])) ? $data['FechaDemanda'] : null;
        $this->PrecioMinimo = (!empty($data['PrecioMinimo'])) ? $data['PrecioMinimo'] : null;
        $this->PrecioMaximo = (!empty($data['PrecioMaximo'])) ? $data['PrecioMaximo'] : null;
        $this->Target = (!empty($data['Target'])) ? $data['Target'] : null;
        $this->Comentarios = (!empty($data['Comentarios'])) ? $data['Comentarios'] : null;
        $this->Actualizaciones = (!empty($data['Actualizaciones'])) ? $data['Actualizaciones'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        //
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        $this->Ruc = (!empty($data['Ruc'])) ? $data['Ruc'] : null;
        $this->CorreoPersonaAtencion = (!empty($data['CorreoPersonaAtencion'])) ? $data['CorreoPersonaAtencion'] : null;
        $this->Campania = (!empty($data['Campania'])) ? $data['Campania'] : null;
        $this->Rubro = (!empty($data['Rubro'])) ? $data['Rubro'] : null;
        $this->EmpresasAdicionales = (!empty($data['EmpresasAdicionales'])) ? $data['EmpresasAdicionales'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
