<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 27/07/16
 * Time: 04:44 PM
 */

namespace Premios\Model;


class CuponPremios
{
    public $id;
    public $BNF3_Segmento_id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $CodigoCupon;
    public $EstadoCupon;
    public $PremiosUsuario;
    public $PremiosUtilizados;
    public $BNF3_Asignacion_Premios_id;
    public $BNF3_Oferta_Premios_id;
    public $BNF3_Oferta_Premios_Atributos_id;
    public $BNF_Rubro_id;
    public $BNF_Categoria_id;
    public $BNF_ClienteCorreo_id;
    public $FechaCreacion;
    public $FechaEliminado;
    public $FechaGenerado;
    public $FechaRedimido;
    public $FechaFinalizado;
    public $FechaCaducado;

    public $PrecioBeneficio;
    public $PrecioVentaPublico;
    public $TituloCorto;
    public $CantidadPremios;
    public $FechaVigencia;
    public $Descarga;
    public $Empresa;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Segmento_id = (!empty($data['BNF3_Segmento_id'])) ? $data['BNF3_Segmento_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->CodigoCupon = (!empty($data['CodigoCupon'])) ? $data['CodigoCupon'] : null;
        $this->EstadoCupon = (!empty($data['EstadoCupon'])) ? $data['EstadoCupon'] : null;
        $this->PremiosUsuario = (!empty($data['PremiosUsuario'])) ? $data['PremiosUsuario'] : 0;
        $this->PremiosUtilizados = (!empty($data['PremiosUtilizados'])) ? $data['PremiosUtilizados'] : 0;
        $this->BNF3_Asignacion_Premios_id = (!empty($data['BNF3_Asignacion_Premios_id']))
            ? $data['BNF3_Asignacion_Premios_id'] : null;
        $this->BNF3_Oferta_Premios_id = (!empty($data['BNF3_Oferta_Premios_id']))
            ? $data['BNF3_Oferta_Premios_id'] : null;
        $this->BNF3_Oferta_Premios_Atributos_id = (!empty($data['BNF3_Oferta_Premios_Atributos_id']))
            ? $data['BNF3_Oferta_Premios_Atributos_id'] : null;
        $this->BNF_Rubro_id = (!empty($data['BNF_Rubro_id'])) ? $data['BNF_Rubro_id'] : null;
        $this->BNF_Categoria_id = (!empty($data['BNF_Categoria_id'])) ? $data['BNF_Categoria_id'] : null;
        $this->BNF_ClienteCorreo_id = (!empty($data['BNF_ClienteCorreo_id'])) ? $data['BNF_ClienteCorreo_id'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaEliminado = (!empty($data['FechaEliminado'])) ? $data['FechaEliminado'] : null;
        $this->FechaGenerado = (!empty($data['FechaGenerado'])) ? $data['FechaGenerado'] : null;
        $this->FechaRedimido = (!empty($data['FechaRedimido'])) ? $data['FechaRedimido'] : null;
        $this->FechaFinalizado = (!empty($data['FechaFinalizado'])) ? $data['FechaFinalizado'] : null;
        $this->FechaCaducado = (!empty($data['FechaCaducado'])) ? $data['FechaCaducado'] : null;

        $this->PrecioBeneficio = (!empty($data['PrecioBeneficio'])) ? $data['PrecioBeneficio'] : null;
        $this->PrecioVentaPublico = (!empty($data['PrecioVentaPublico'])) ? $data['PrecioVentaPublico'] : null;
        $this->TituloCorto = (!empty($data['TituloCorto'])) ? $data['TituloCorto'] : null;
        $this->CantidadPremios = (!empty($data['CantidadPremios'])) ? $data['CantidadPremios'] : null;
        $this->FechaVigencia = (!empty($data['FechaVigencia'])) ? $data['FechaVigencia'] : null;
        $this->Descarga = (!empty($data['Descarga'])) ? $data['Descarga'] : false;
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}