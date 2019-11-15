<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 11/11/15
 * Time: 07:22 PM
 */
namespace Reportes\Model;

class OfertaFormCliente
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Cliente_id;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;

        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        $this->Total = (!empty($data['Total'])) ? $data['Total'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}