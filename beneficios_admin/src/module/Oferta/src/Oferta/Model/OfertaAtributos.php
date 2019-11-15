<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 20/10/16
 * Time: 04:54 PM
 */

namespace Oferta\Model;


class OfertaAtributos
{
    public $id;
    public $BNF_Oferta_id;
    public $NombreAtributo;
    public $DatoBeneficio;
    public $Stock;
    public $StockInicial;
    public $FechaVigencia;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->NombreAtributo = (!empty($data['NombreAtributo'])) ? $data['NombreAtributo'] : null;
        $this->DatoBeneficio = (!empty($data['DatoBeneficio'])) ? $data['DatoBeneficio'] : null;
        $this->Stock = (!empty($data['Stock'])) ? $data['Stock'] : null;
        $this->StockInicial = (!empty($data['StockInicial'])) ? $data['StockInicial'] : null;
        $this->FechaVigencia = (!empty($data['FechaVigencia'])) ? $data['FechaVigencia'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
