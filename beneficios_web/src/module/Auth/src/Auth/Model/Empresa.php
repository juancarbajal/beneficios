<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/10/15
 * Time: 05:08 PM
 */

namespace Auth\Model;

class Empresa
{
    public $id;
    public $BNF_Usuario_id;
    public $BNF_TipoDocumento_id;
    public $BNF_Ubigeo_id_envio;
    public $BNF_Ubigeo_id_legal;
    public $NombreComercial;
    public $RazonSocial;
    public $ApellidoPaterno;
    public $ApellidoMaterno;
    public $Nombre;
    public $Ruc;
    public $Descripcion;
    public $RepresentanteLegal;
    public $RepresentanteNumeroDocumento;
    public $DireccionLegal;
    public $DireccionEnvio;
    public $HoraAtencion;
    public $HoraAtencionInicio;
    public $HoraAtencionFin;
    public $PersonaAtencion;
    public $CargoPersonaAtencion;
    public $checkboxLogo;
    public $checkboxLogoBeneficio;
    public $checkboxMoney;
    public $checkboxTotalPuntos;
    public $Telefono;
    public $Celular;
    public $CorreoPersonaAtencion;
    public $Logo;
    public $IdSap;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;
    public $ClaseEmpresaCliente;
    public $Color_menu;
    public $Color_hover;
    public $SubDominio;
    public $Slug;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Usuario_id = (!empty($data['BNF_Usuario_id'])) ? $data['BNF_Usuario_id'] : null;
        $this->BNF_TipoDocumento_id = (!empty($data['BNF_TipoDocumento_id'])) ? $data['BNF_TipoDocumento_id'] : null;
        $this->BNF_Ubigeo_id_envio = (!empty($data['BNF_Ubigeo_id_envio'])) ? $data['BNF_Ubigeo_id_envio'] : null;
        $this->BNF_Ubigeo_id_legal = (!empty($data['BNF_Ubigeo_id_legal'])) ? $data['BNF_Ubigeo_id_legal'] : null;
        $this->NombreComercial = (!empty($data['NombreComercial'])) ? $data['NombreComercial'] : null;
        $this->RazonSocial = (!empty($data['RazonSocial'])) ? $data['RazonSocial'] : null;
        $this->ApellidoPaterno = (!empty($data['ApellidoPaterno'])) ? $data['ApellidoPaterno'] : null;
        $this->ApellidoMaterno = (!empty($data['ApellidoMaterno'])) ? $data['ApellidoMaterno'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Ruc = (!empty($data['Ruc'])) ? $data['Ruc'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->RepresentanteLegal = (!empty($data['RepresentanteLegal'])) ? $data['RepresentanteLegal'] : null;
        $this->RepresentanteNumeroDocumento = (
        !empty($data['RepresentanteNumeroDocumento'])) ? $data['RepresentanteNumeroDocumento'] : null;
        $this->DireccionLegal = (!empty($data['DireccionLegal'])) ? $data['DireccionLegal'] : null;
        $this->DireccionEnvio = (!empty($data['DireccionEnvio'])) ? $data['DireccionEnvio'] : null;
        $this->HoraAtencion = (!empty($data['HoraAtencion'])) ? $data['HoraAtencion'] : null;
        $this->HoraAtencionInicio = (!empty($data['HoraAtencionInicio'])) ? $data['HoraAtencionInicio'] : null;
        $this->HoraAtencionFin = (!empty($data['HoraAtencionFin'])) ? $data['HoraAtencionFin'] : null;
        $this->PersonaAtencion = (!empty($data['PersonaAtencion'])) ? $data['PersonaAtencion'] : null;
        $this->CargoPersonaAtencion = (!empty($data['CargoPersonaAtencion'])) ? $data['CargoPersonaAtencion'] : null;
        $this->Telefono = (!empty($data['Telefono'])) ? $data['Telefono'] : null;
        $this->Celular = (!empty($data['Celular'])) ? $data['Celular'] : null;
        $this->CorreoPersonaAtencion = (!empty($data['CorreoPersonaAtencion'])) ? $data['CorreoPersonaAtencion'] : null;
        $this->Logo = (!empty($data['Logo'])) ? $data['Logo'] : null;
        $this->IdSap = (!empty($data['IdSap'])) ? $data['IdSap'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->ClaseEmpresaCliente = (!empty($data['ClaseEmpresaCliente'])) ? $data['ClaseEmpresaCliente'] : null;
        $this->Color_menu = (!empty($data['Color_menu'])) ? $data['Color_menu'] : null;
        $this->Color_hover = (!empty($data['Color_hover'])) ? $data['Color_hover'] : null;
        $this->SubDominio = (!empty($data['SubDominio'])) ? $data['SubDominio'] : null;
        $this->checkboxLogo = (!empty($data['checkboxLogo'])) ? $data['checkboxLogo'] : null;
        $this->checkboxMoney = (!empty($data['checkboxMoney'])) ? $data['checkboxMoney'] : null;
        $this->checkboxTotalPuntos = (!empty($data['checkboxTotalPuntos'])) ? $data['checkboxTotalPuntos'] : null;

        $this->checkboxLogoBeneficio = (!empty($data['checkboxLogoBeneficio'])) ? $data['checkboxLogoBeneficio'] : null;
        $this->Slug = (!empty($data['Slug'])) ? $data['Slug'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
