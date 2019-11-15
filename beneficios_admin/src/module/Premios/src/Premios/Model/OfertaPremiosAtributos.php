<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:11 PM
 */

namespace Premios\Model;

class OfertaPremiosAtributos
{
    public $id;
    public $BNF3_Oferta_Premios_id;
    public $NombreAtributo;
    public $PrecioVentaPublico;
    public $PrecioBeneficio;
    public $Stock;
    public $FechaVigencia;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Oferta_Premios_id = (!empty($data['BNF3_Oferta_Premios_id'])) ? $data['BNF3_Oferta_Premios_id'] : null;
        $this->NombreAtributo = (!empty($data['NombreAtributo'])) ? $data['NombreAtributo'] : null;
        $this->PrecioVentaPublico = (!empty($data['PrecioVentaPublico'])) ? $data['PrecioVentaPublico'] : null;
        $this->PrecioBeneficio = (!empty($data['PrecioBeneficio'])) ? $data['PrecioBeneficio'] : null;
        $this->Stock = (!empty($data['Stock'])) ? $data['Stock'] : null;
        $this->FechaVigencia = (!empty($data['FechaVigencia'])) ? $data['FechaVigencia'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
