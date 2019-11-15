<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 12:27 PM
 */

namespace Application\Model;

class Oferta
{
    public $id;
    public $BNF_TipoBeneficio_id;
    public $Nombre;
    public $Titulo;
    public $TituloCorto;
    public $SubTitulo;
    public $FormatoBeneficio;
    public $DatoBeneficio;
    public $Descripcion;
    public $CondicionesUso;
    public $Direccion;
    public $Telefono;
    public $Premium;
    public $Distrito;
    public $FechaInicioVigencia;
    public $FechaFinVigencia;
    public $FechaInicioPublicacion;
    public $FechaFinPublicacion;
    public $Stock;
    public $Correo;
    public $Estado;
    public $DescargaMaximaDia;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;
    public $BNF_BolsaTotal_TipoPaquete_id;
    public $BNF_BolsaTotal_Empresa_id;
    public $TipoOferta;
    public $imagenOferta;
    public $LogoEmpresa;
    public $nombreEmpresa;
    public $total;
    public $Slug;
    public $CorreoContacto;
    public $CondicionesDelivery;
    public $CondicionesDeliveryTexto;
    public $CondicionesDeliveryEstado;
    public $CondicionesTebca;
    public $TipoAtributo;
    public $TipoEspecial;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_TipoBeneficio_id = (!empty($data['BNF_TipoBeneficio_id'])) ? $data['BNF_TipoBeneficio_id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Titulo = (!empty($data['Titulo'])) ? $data['Titulo'] : null;
        $this->TituloCorto = (!empty($data['TituloCorto'])) ? $data['TituloCorto'] : null;
        $this->SubTitulo = (!empty($data['SubTitulo'])) ? $data['SubTitulo'] : null;
        $this->FormatoBeneficio = (!empty($data['FormatoBeneficio'])) ? $data['FormatoBeneficio'] : null;
        $this->DatoBeneficio = (!empty($data['DatoBeneficio'])) ? $data['DatoBeneficio'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->CondicionesUso = (!empty($data['CondicionesUso'])) ? $data['CondicionesUso'] : null;
        $this->Direccion = (!empty($data['Direccion'])) ? $data['Direccion'] : null;
        $this->Telefono = (!empty($data['Telefono'])) ? $data['Telefono'] : null;
        $this->Premium = (!empty($data['Premium'])) ? $data['Premium'] : null;
        $this->Distrito = (!empty($data['Distrito'])) ? $data['Distrito'] : null;
        $this->FechaInicioVigencia = (!empty($data['FechaInicioVigencia'])) ? $data['FechaInicioVigencia'] : null;
        $this->FechaFinVigencia = (!empty($data['FechaFinVigencia'])) ? $data['FechaFinVigencia'] : null;
        $this->FechaInicioPublicacion = (
        !empty($data['FechaInicioPublicacion'])) ? $data['FechaInicioPublicacion'] : null;
        $this->FechaFinPublicacion = (!empty($data['FechaFinPublicacion'])) ? $data['FechaFinPublicacion'] : null;
        $this->Stock = (!empty($data['Stock'])) ? $data['Stock'] : null;
        $this->Correo = (!empty($data['Correo'])) ? $data['Correo'] : null;
        $this->Estado = (!empty($data['Estado'])) ? $data['Estado'] : null;
        $this->DescargaMaximaDia = (!empty($data['DescargaMaximaDia'])) ? $data['DescargaMaximaDia'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->BNF_BolsaTotal_TipoPaquete_id = (!empty($data['BNF_BolsaTotal_TipoPaquete_id']))
            ? $data['BNF_BolsaTotal_TipoPaquete_id'] : null;
        $this->BNF_BolsaTotal_Empresa_id = (!empty($data['BNF_BolsaTotal_Empresa_id']))
            ? $data['BNF_BolsaTotal_Empresa_id'] : null;
        $this->TipoOferta = (!empty($data['TipoOferta'])) ? $data['TipoOferta'] : null;
        $this->imagenOferta = (!empty($data['imagenOferta'])) ? $data['imagenOferta'] : null;
        $this->LogoEmpresa = (!empty($data['LogoEmpresa'])) ? $data['LogoEmpresa'] : null;
        $this->nombreEmpresa = (!empty($data['nombreEmpresa'])) ? $data['nombreEmpresa'] : null;
        $this->total = (!empty($data['total'])) ? $data['total'] : null;
        $this->Slug = (!empty($data['Slug'])) ? $data['Slug'] : null;
        $this->CorreoContacto = (!empty($data['CorreoContacto'])) ? $data['CorreoContacto'] : null;
        $this->CondicionesDelivery = (!empty($data['CondicionesDelivery'])) ? $data['CondicionesDelivery'] : null;
        $this->CondicionesDeliveryTexto = (!empty($data['CondicionesDeliveryTexto']))
            ? $data['CondicionesDeliveryTexto'] : null;
        $this->CondicionesDeliveryEstado = (!empty($data['CondicionesDeliveryEstado']))
            ? $data['CondicionesDeliveryEstado'] : null;
        $this->CondicionesTebca = (!empty($data['CondicionesTebca'])) ? $data['CondicionesTebca'] : null;
        $this->TipoAtributo = (!empty($data['TipoAtributo'])) ? $data['TipoAtributo'] : null;
        $this->TipoEspecial = (!empty($data['TipoEspecial'])) ? $data['TipoEspecial'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
