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
    public $BNF3_Segmento_id;
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

    public $ImagenOferta;
    public $TotalOfertas;

    public $Empresa;
    public $SlugEmpresa;
    public $LogoEmpresa;
    public $DescripcionEmpresa;
    public $WebEmpresa;
    public $DireccionEmpresa;
    public $TelefonoEmpresa;

    public $TotalCupones;
    public $CaducadoTiempo;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Segmento_id = (!empty($data['BNF3_Segmento_id'])) ? $data['BNF3_Segmento_id'] : null;
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

        $this->ImagenOferta = (!empty($data['ImagenOferta'])) ? $data['ImagenOferta'] : null;
        $this->TotalOfertas = (!empty($data['TotalOfertas'])) ? $data['TotalOfertas'] : null;
        $this->CaducadoTiempo = (!empty($data['CaducadoTiempo'])) ? $data['CaducadoTiempo'] : null;
        $this->TotalCupones = (!empty($data['TotalCupones'])) ? $data['TotalCupones'] : null;

        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        $this->SlugEmpresa = (!empty($data['SlugEmpresa'])) ? $data['SlugEmpresa'] : null;
        $this->LogoEmpresa = (!empty($data['LogoEmpresa'])) ? $data['LogoEmpresa'] : null;
        $this->DescripcionEmpresa = (!empty($data['DescripcionEmpresa'])) ? $data['DescripcionEmpresa'] : null;
        $this->WebEmpresa = (!empty($data['WebEmpresa'])) ? $data['WebEmpresa'] : null;
        $this->DireccionEmpresa = (!empty($data['DireccionEmpresa'])) ? $data['DireccionEmpresa'] : null;
        $this->TelefonoEmpresa = (!empty($data['TelefonoEmpresa'])) ? $data['TelefonoEmpresa'] : null;
        $this->EmailEmpresa = (!empty($data['EmailEmpresa'])) ? $data['EmailEmpresa'] : null;

        $this->HoraInicioContacto = (!empty($data['HoraInicioContacto'])) ? $data['HoraInicioContacto'] : null;
        $this->HoraFinContacto = (!empty($data['HoraFinContacto'])) ? $data['HoraFinContacto'] : null;
        $this->NombreContacto = (!empty($data['NombreContacto'])) ? $data['NombreContacto'] : null;
        $this->TelefonoContacto = (!empty($data['TelefonoContacto'])) ? $data['TelefonoContacto'] : null;
        $this->CorreoContacto = (!empty($data['CorreoContacto'])) ? $data['CorreoContacto'] : null;

        $this->DireccionOferta = (!empty($data['DireccionOferta'])) ? $data['DireccionOferta'] : null;
        $this->TelefonoOferta = (!empty($data['TelefonoOferta'])) ? $data['TelefonoOferta'] : null;
        $this->DiasAtencionContacto = (!empty($data['DiasAtencionContacto'])) ? $data['DiasAtencionContacto'] : null;
        $this->DiasAtencionContacto = (!empty($data['DiasAtencionContacto'])) ? $data['DiasAtencionContacto'] : null;

        $this->idCategoria = (!empty($data['idCategoria'])) ? $data['idCategoria'] : null;
        $this->idCampania = (!empty($data['idCampania'])) ? $data['idCampania'] : null;
        $this->idOferta = (!empty($data['idOferta'])) ? $data['idOferta'] : null;
        $this->TituloOferta = (!empty($data['TituloOferta'])) ? $data['TituloOferta'] : null;
        $this->CondicionesTebca = (!empty($data['CondicionesTebca'])) ? $data['CondicionesTebca'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        return $this->id;
    }
}
