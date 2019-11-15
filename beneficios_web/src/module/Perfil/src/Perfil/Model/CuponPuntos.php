<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 27/07/16
 * Time: 04:44 PM
 */

namespace Perfil\Model;


class CuponPuntos
{
    public $id;
    public $BNF2_Segmento_id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $CodigoCupon;
    public $EstadoCupon;
    public $PuntosUsuario;
    public $PuntosUtilizados;
    public $BNF2_Asignacion_Puntos_id;
    public $BNF2_Oferta_Puntos_id;
    public $BNF2_Oferta_Puntos_Atributos_id;
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
    public $CantidadPuntos;
    public $FechaVigencia;
    public $Descarga;
    public $Empresa;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Segmento_id = (!empty($data['BNF2_Segmento_id'])) ? $data['BNF2_Segmento_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->CodigoCupon = (!empty($data['CodigoCupon'])) ? $data['CodigoCupon'] : null;
        $this->EstadoCupon = (!empty($data['EstadoCupon'])) ? $data['EstadoCupon'] : null;
        $this->PuntosUsuario = (!empty($data['PuntosUsuario'])) ? $data['PuntosUsuario'] : 0;
        $this->PuntosUtilizados = (!empty($data['PuntosUtilizados'])) ? $data['PuntosUtilizados'] : 0;
        $this->BNF2_Asignacion_Puntos_id = (!empty($data['BNF2_Asignacion_Puntos_id']))
            ? $data['BNF2_Asignacion_Puntos_id'] : null;
        $this->BNF2_Oferta_Puntos_id = (!empty($data['BNF2_Oferta_Puntos_id']))
            ? $data['BNF2_Oferta_Puntos_id'] : null;
        $this->BNF2_Oferta_Puntos_Atributos_id = (!empty($data['BNF2_Oferta_Puntos_Atributos_id']))
            ? $data['BNF2_Oferta_Puntos_Atributos_id'] : null;
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
        $this->CantidadPuntos = (!empty($data['CantidadPuntos'])) ? $data['CantidadPuntos'] : null;
        $this->FechaVigencia = (!empty($data['FechaVigencia'])) ? $data['FechaVigencia'] : null;
        $this->Descarga = (!empty($data['Descarga'])) ? $data['Descarga'] : false;
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}