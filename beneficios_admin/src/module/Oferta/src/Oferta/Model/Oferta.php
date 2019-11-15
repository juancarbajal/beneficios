<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 12:27 PM
 */

namespace Oferta\Model;

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
    public $FechaFinVigencia;
    public $FechaInicioPublicacion;
    public $FechaFinPublicacion;
    public $Stock;
    public $StockInicial;
    public $Correo;
    public $Estado;
    public $DescargaMaximaDia;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;
    public $BNF_BolsaTotal_TipoPaquete_id;
    public $BNF_BolsaTotal_Empresa_id;
    public $TipoOferta;
    public $Asignaciones;
    public $Slug;
    public $CondicionesDelivery;
    public $CondicionesDeliveryEstado;
    public $CondicionesDeliveryTexto;
    public $CondicionesTebca;
    public $TipoAtributo;
    public $Atributo_id;
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
        $this->FechaFinVigencia = (!empty($data['FechaFinVigencia'])) ? $data['FechaFinVigencia'] : null;
        $this->FechaInicioPublicacion = (!empty($data['FechaInicioPublicacion']))
            ? $data['FechaInicioPublicacion'] : null;
        $this->FechaFinPublicacion = (!empty($data['FechaFinPublicacion'])) ? $data['FechaFinPublicacion'] : null;
        $this->Stock = (!empty($data['Stock'])) ? $data['Stock'] : null;
        $this->StockInicial = (!empty($data['StockInicial'])) ? $data['StockInicial'] : null;
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
        $this->Asignaciones = (!empty($data['Asignaciones'])) ? $data['Asignaciones'] : null;
        $this->Slug = (!empty($data['Slug'])) ? $data['Slug'] : null;
        $this->CondicionesDelivery = (!empty($data['CondicionesDelivery'])) ? $data['CondicionesDelivery'] : null;
        $this->CondicionesDeliveryEstado = (!empty($data['CondicionesDeliveryEstado']))
            ? $data['CondicionesDeliveryEstado'] : null;
        $this->CondicionesDeliveryTexto = (!empty($data['CondicionesDeliveryTexto']))
            ? $data['CondicionesDeliveryTexto'] : null;
        $this->CondicionesTebca = (!empty($data['CondicionesTebca'])) ? $data['CondicionesTebca'] : null;
        $this->TipoAtributo = (!empty($data['TipoAtributo'])) ? $data['TipoAtributo'] : null;
        $this->TipoEspecial = (!empty($data['TipoEspecial'])) ? $data['TipoEspecial'] : null;

        $this->Descargados = (!empty($data['Descargados'])) ? $data['Descargados'] : null;
        $this->NoUtilizados = (!empty($data['NoUtilizados'])) ? $data['NoUtilizados'] : null;
        $this->Redimidos = (!empty($data['Redimidos'])) ? $data['Redimidos'] : null;

        $this->NombreComercial = (!empty($data['NombreComercial'])) ? $data['NombreComercial'] : null;
        $this->Categoria = (!empty($data['Categoria'])) ? $data['Categoria'] : null;
        $this->Descargas = (!empty($data['Descargas'])) ? $data['Descargas'] : 0;
        $this->Atributo_id = (!empty($data['Atributo_id'])) ? $data['Atributo_id'] : 0;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
