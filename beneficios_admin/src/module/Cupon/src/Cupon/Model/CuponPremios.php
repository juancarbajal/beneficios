<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/07/16
 * Time: 06:01 PM
 */

namespace Cupon\Model;

class CuponPremios
{
    public $id;
    public $BNF3_Oferta_Empresa_id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $CodigoCupon;
    public $EstadoCupon;
    public $PremiosUtilizados;
    public $BNF3_Asignacion_Premios_id;
    public $BNF3_Oferta_Premios_id;
    public $BNF3_Oferta_Premios_Atributos_id;
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
    public $Campania;
    public $Oferta;
    public $PrecioVentaPublico;
    public $PrecioBeneficio;
    public $FechaVigencia;
    public $Empresa;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Oferta_Empresa_id =
            (!empty($data['BNF3_Oferta_Empresa_id'])) ? $data['BNF3_Oferta_Empresa_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->CodigoCupon = (!empty($data['CodigoCupon'])) ? $data['CodigoCupon'] : null;
        $this->EstadoCupon = (!empty($data['EstadoCupon'])) ? $data['EstadoCupon'] : null;
        $this->PremiosUtilizados = (!empty($data['PremiosUtilizados'])) ? $data['PremiosUtilizados'] : null;
        $this->BNF3_Asignacion_Premios_id = (!empty($data['BNF3_Asignacion_Premios_id']))
            ? $data['BNF3_Asignacion_Premios_id'] : null;
        $this->BNF3_Oferta_Premios_id = (!empty($data['BNF3_Oferta_Premios_id']))
            ? $data['BNF3_Oferta_Premios_id'] : null;
        $this->BNF3_Oferta_Premios_Atributos_id = (!empty($data['BNF3_Oferta_Premios_Atributos_id']))
            ? $data['BNF3_Oferta_Premios_Atributos_id'] : null;
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
