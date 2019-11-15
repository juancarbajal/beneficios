<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/09/15
 * Time: 10:30 PM
 */

namespace Oferta\Model;


class OfertaEmpresaCliente
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Empresa_id;
    public $NumeroCupones;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->NumeroCupones = (!empty($data['NumeroCupones'])) ? $data['NumeroCupones'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
