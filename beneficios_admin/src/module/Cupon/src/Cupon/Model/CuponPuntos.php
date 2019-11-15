<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/07/16
 * Time: 06:01 PM
 */

namespace Cupon\Model;

class CuponPuntos
{
    public $id;
    public $BNF2_Oferta_Empresa_id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $CodigoCupon;
    public $EstadoCupon;
    public $PuntosUtilizados;
    public $BNF2_Asignacion_Puntos_id;
    public $BNF2_Oferta_Puntos_id;
    public $BNF2_Oferta_Puntos_Atributos_id;
    public $FechaCreacion;
    public $FechaEliminado;
    public $FechaGenerado;
    public $FechaRedimido;
    public $FechaPorPagar;
    public $FechaPagado;
    public $FechaStandBy;
    public $FechaAnulado;
    public $FechaFinalizado;
    public $FechaCaducado;

    public $UltimaActualizacion;
    public $ComentarioUno;
    public $ComentarioDos;
    public $Campania;
    public $Oferta;
    public $PrecioVentaPublico;
    public $PrecioBeneficio;
    public $FechaVigencia;
    public $Empresa;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Oferta_Empresa_id =
            (!empty($data['BNF2_Oferta_Empresa_id'])) ? $data['BNF2_Oferta_Empresa_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->CodigoCupon = (!empty($data['CodigoCupon'])) ? $data['CodigoCupon'] : null;
        $this->EstadoCupon = (!empty($data['EstadoCupon'])) ? $data['EstadoCupon'] : null;
        $this->PuntosUtilizados = (!empty($data['PuntosUtilizados'])) ? $data['PuntosUtilizados'] : null;
        $this->BNF2_Asignacion_Puntos_id = (!empty($data['BNF2_Asignacion_Puntos_id']))
            ? $data['BNF2_Asignacion_Puntos_id'] : null;
        $this->BNF2_Oferta_Puntos_id = (!empty($data['BNF2_Oferta_Puntos_id']))
            ? $data['BNF2_Oferta_Puntos_id'] : null;
        $this->BNF2_Oferta_Puntos_Atributos_id = (!empty($data['BNF2_Oferta_Puntos_Atributos_id']))
            ? $data['BNF2_Oferta_Puntos_Atributos_id'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaEliminado = (!empty($data['FechaEliminado'])) ? $data['FechaEliminado'] : null;
        $this->FechaGenerado = (!empty($data['FechaGenerado'])) ? $data['FechaGenerado'] : null;
        $this->FechaRedimido = (!empty($data['FechaRedimido'])) ? $data['FechaRedimido'] : null;
        $this->FechaPorPagar = (!empty($data['FechaPorPagar'])) ? $data['FechaPorPagar'] : null;
        $this->FechaPagado = (!empty($data['FechaPagado'])) ? $data['FechaPagado'] : null;
        $this->FechaStandBy = (!empty($data['FechaStandBy'])) ? $data['FechaStandBy'] : null;
        $this->FechaAnulado = (!empty($data['FechaAnulado'])) ? $data['FechaAnulado'] : null;
        $this->FechaFinalizado = (!empty($data['FechaFinalizado'])) ? $data['FechaFinalizado'] : null;
        $this->FechaCaducado = (!empty($data['FechaCaducado'])) ? $data['FechaCaducado'] : null;


        $this->ComentarioUno = (!empty($data['ComentarioUno'])) ? $data['ComentarioUno'] : null;

        $this->ComentarioDos = (!empty($data['ComentarioDos'])) ? $data['ComentarioDos'] : null;


        $this->UltimaActualizacion = (!empty($data['UltimaActualizacion'])) ? $data['UltimaActualizacion'] : null;
        $this->Campania = (!empty($data['Campania'])) ? $data['Campania'] : null;
        $this->Oferta = (!empty($data['Oferta'])) ? $data['Oferta'] : null;
        $this->PrecioVentaPublico = (!empty($data['PrecioVentaPublico'])) ? $data['PrecioVentaPublico'] : null;
        $this->PrecioBeneficio = (!empty($data['PrecioBeneficio'])) ? $data['PrecioBeneficio'] : null;
        $this->FechaVigencia = (!empty($data['FechaVigencia'])) ? $data['FechaVigencia'] : null;
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
