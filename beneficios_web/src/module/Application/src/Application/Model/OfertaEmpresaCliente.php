<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 21/10/15
 * Time: 18:46
 */

namespace Application\Model;

class OfertaEmpresaCliente
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Empresa_id;
    public $NumeroCupones;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public $LogoEmpresa;
    public $imagenOferta;
    public $nombreEmpresa;
    public $imgOfertaPrincipal;

    public $idOferta;
    public $TituloCortoOferta;
    public $vigencia;
    public $datoBeneficio;
    public $idTipoBeneficio;
    public $totalOfertasEP;
    public $Premium;
    public $Titulo;

    public $SlugEmpresa;
    public $SlugOferta;

    public $TituloOferta;
    public $condicionesUso;
    public $TipoOferta;

    public $DescripcionEmpresa;
    public $DireccionEmpresa;
    public $webEmpresa;
    public $TelefonoEmpresa;

    public $idCategoria;
    public $idCampania;

    public $DiasAtencionContacto;
    public $HoraInicioContacto;
    public $HoraFinContacto;
    public $NombreContacto;
    public $TelefonoContacto;
    public $CorreoContacto;

    public $emailEmpresa;

    public $DireccionOferta;
    public $TelefonoOferta;

    public $caducadoTiempo;
    public $EstadoOferta;
    public $CondicionesTebca;
    public $TipoAtributo;
    public $TipoEspecial;
    public $checkboxLogo;
    public $Stock;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->NumeroCupones = (!empty($data['NumeroCupones'])) ? $data['NumeroCupones'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;

        $this->LogoEmpresa = (!empty($data['LogoEmpresa'])) ? $data['LogoEmpresa'] : null;
        $this->imagenOferta = (!empty($data['imagenOferta'])) ? $data['imagenOferta'] : null;
        $this->nombreEmpresa = (!empty($data['nombreEmpresa'])) ? $data['nombreEmpresa'] : null;
        $this->imgOfertaPrincipal = (!empty($data['imgOfertaPrincipal'])) ? $data['imgOfertaPrincipal'] : null;

        $this->idOferta = (!empty($data['idOferta'])) ? $data['idOferta'] : null;
        $this->TituloCortoOferta = (!empty($data['TituloCortoOferta'])) ? $data['TituloCortoOferta'] : null;
        $this->vigencia = (!empty($data['vigencia'])) ? $data['vigencia'] : null;
        $this->datoBeneficio = (!empty($data['datoBeneficio'])) ? $data['datoBeneficio'] : null;
        $this->idTipoBeneficio = (!empty($data['idTipoBeneficio'])) ? $data['idTipoBeneficio'] : null;
        $this->totalOfertasEP = (!empty($data['totalOfertasEP'])) ? $data['totalOfertasEP'] : null;
        $this->Premium = (!empty($data['Premium'])) ? $data['Premium'] : null;
        $this->Titulo = (!empty($data['Titulo'])) ? $data['Titulo'] : null;

        $this->SlugEmpresa = (!empty($data['SlugEmpresa'])) ? $data['SlugEmpresa'] : null;
        $this->SlugOferta = (!empty($data['SlugOferta'])) ? $data['SlugOferta'] : null;

        $this->TituloOferta = (!empty($data['TituloOferta'])) ? $data['TituloOferta'] : null;
        $this->condicionesUso = (!empty($data['condicionesUso'])) ? $data['condicionesUso'] : null;
        $this->TipoOferta = (!empty($data['TipoOferta'])) ? $data['TipoOferta'] : null;

        $this->DescripcionEmpresa = (!empty($data['DescripcionEmpresa'])) ? $data['DescripcionEmpresa'] : null;
        $this->DireccionEmpresa = (!empty($data['DireccionEmpresa'])) ? $data['DireccionEmpresa'] : null;
        $this->webEmpresa = (!empty($data['webEmpresa'])) ? $data['webEmpresa'] : null;
        $this->TelefonoEmpresa = (!empty($data['TelefonoEmpresa'])) ? $data['TelefonoEmpresa'] : null;

        $this->idCategoria = (!empty($data['idCategoria'])) ? $data['idCategoria'] : null;
        $this->idCampania = (!empty($data['idCampania'])) ? $data['idCampania'] : null;

        $this->DiasAtencionContacto = (!empty($data['DiasAtencionContacto'])) ? $data['DiasAtencionContacto'] : null;
        $this->HoraInicioContacto = (!empty($data['HoraInicioContacto'])) ? $data['HoraInicioContacto'] : null;
        $this->HoraFinContacto = (!empty($data['HoraFinContacto'])) ? $data['HoraFinContacto'] : null;
        $this->NombreContacto = (!empty($data['NombreContacto'])) ? $data['NombreContacto'] : null;
        $this->TelefonoContacto = (!empty($data['TelefonoContacto'])) ? $data['TelefonoContacto'] : null;
        $this->CorreoContacto = (!empty($data['CorreoContacto'])) ? $data['CorreoContacto'] : null;

        $this->emailEmpresa = (!empty($data['emailEmpresa'])) ? $data['emailEmpresa'] : null;

        $this->DireccionOferta = (!empty($data['DireccionOferta'])) ? $data['DireccionOferta'] : null;
        $this->TelefonoOferta = (!empty($data['TelefonoOferta'])) ? $data['TelefonoOferta'] : null;

        $this->caducadoTiempo = (!empty($data['caducadoTiempo'])) ? $data['caducadoTiempo'] : null;
        $this->EstadoOferta = (!empty($data['EstadoOferta'])) ? $data['EstadoOferta'] : null;
        $this->CondicionesTebca = (!empty($data['CondicionesTebca'])) ? $data['CondicionesTebca'] : null;
        $this->TipoAtributo = (!empty($data['TipoAtributo'])) ? $data['TipoAtributo'] : null;
        $this->TipoEspecial = (!empty($data['TipoEspecial'])) ? $data['TipoEspecial'] : null;
        $this->checkboxLogo = (!empty($data['checkboxLogo'])) ? $data['checkboxLogo'] : null;
        $this->Stock = (!empty($data['Stock'])) ? $data['Stock'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        return $this->idOferta;
    }
}
