<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:10 PM
 */

namespace Premios\Model;

class OfertaPremios
{
    public $id;
    public $BNF_Empresa_id;
    public $Nombre;
    public $Titulo;
    public $TituloCorto;
    public $CondicionesUso;
    public $Direccion;
    public $Telefono;
    public $Correo;
    public $Premium;
    public $TipoPrecio;
    public $PrecioVentaPublico;
    public $PrecioBeneficio;
    public $Distrito;
    public $FechaVigencia;
    public $DescargaMaxima;
    public $Stock;
    public $Slug;
    public $Estado;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Empresa;
    public $Segmentos;
    public $Campania;
    public $Rubro;
    public $Atributo_id;
    public $Atributo;
    public $Pagados;
    public $VigenciaCampania;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Titulo = (!empty($data['Titulo'])) ? $data['Titulo'] : null;
        $this->TituloCorto = (!empty($data['TituloCorto'])) ? $data['TituloCorto'] : null;
        $this->CondicionesUso = (!empty($data['CondicionesUso'])) ? $data['CondicionesUso'] : null;
        $this->Direccion = (!empty($data['Direccion'])) ? $data['Direccion'] : null;
        $this->Telefono = (!empty($data['Telefono'])) ? $data['Telefono'] : null;
        $this->Correo = (!empty($data['Correo'])) ? $data['Correo'] : null;
        $this->Premium = (!empty($data['Premium'])) ? $data['Premium'] : null;
        $this->TipoPrecio = (!empty($data['TipoPrecio'])) ? $data['TipoPrecio'] : null;
        $this->PrecioVentaPublico = (!empty($data['PrecioVentaPublico'])) ? $data['PrecioVentaPublico'] : null;
        $this->PrecioBeneficio = (!empty($data['PrecioBeneficio'])) ? $data['PrecioBeneficio'] : null;
        $this->Distrito = (!empty($data['Distrito'])) ? $data['Distrito'] : null;
        $this->FechaVigencia = (!empty($data['FechaVigencia'])) ? $data['FechaVigencia'] : null;
        $this->DescargaMaxima = (!empty($data['DescargaMaxima'])) ? $data['DescargaMaxima'] : null;
        $this->Stock = (!empty($data['Stock'])) ? $data['Stock'] : null;
        $this->Slug = (!empty($data['Slug'])) ? $data['Slug'] : null;
        $this->Estado = (!empty($data['Estado'])) ? $data['Estado'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        $this->Segmentos = (!empty($data['Segmentos'])) ? $data['Segmentos'] : null;
        $this->Campania = (!empty($data['Campania'])) ? $data['Campania'] : null;
        $this->Rubro = (!empty($data['Rubro'])) ? $data['Rubro'] : null;
        $this->Atributo_id = (!empty($data['Atributo_id'])) ? $data['Atributo_id'] : null;
        $this->Atributo = (!empty($data['Atributo'])) ? $data['Atributo'] : null;

        $this->Descargas = (!empty($data['Descargas'])) ? $data['Descargas'] : null;
        $this->Redimidas = (!empty($data['Redimidas'])) ? $data['Redimidas'] : null;
        $this->Pagados = (!empty($data['Pagados'])) ? $data['Pagados'] : null;
        $this->VigenciaCampania = (!empty($data['VigenciaCampania'])) ? $data['VigenciaCampania'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
