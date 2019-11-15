<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 12:27 PM
 */

namespace Application\Model;

class OfertaCuponCodigo
{
    public $id;
    public $BNF_Oferta_id;
    public $Codigo;
    public $Estado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->Codigo = (!empty($data['Codigo'])) ? $data['Codigo'] : null;
        $this->Estado = (!empty($data['Estado'])) ? $data['Estado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
