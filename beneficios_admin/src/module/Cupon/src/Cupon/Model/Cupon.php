<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 06/10/15
 * Time: 04:39 PM
 */

namespace Cupon\Model;

class Cupon
{
    public $id;
    public $BNF_OfertaEmpresaCliente_id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $EstadoCupon;
    public $BNF_Oferta_id;
    public $BNF_Oferta_Atributo_id;
    public $FechaCreacion;
    public $FechaEliminado;
    public $FechaGenerado;
    public $FechaRedimido;
    public $FechaFinalizado;
    public $FechaCaducado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_OfertaEmpresaCliente_id = (!empty($data['BNF_OfertaEmpresaCliente_id']))
            ? $data['BNF_OfertaEmpresaCliente_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->EstadoCupon = (!empty($data['EstadoCupon'])) ? $data['EstadoCupon'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Oferta_Atributo_id = (!empty($data['BNF_Oferta_Atributo_id']))
            ? $data['BNF_Oferta_Atributo_id'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaGenerado = (!empty($data['FechaGenerado'])) ? $data['FechaGenerado'] : null;
        $this->FechaRedimido = (!empty($data['FechaRedimido'])) ? $data['FechaRedimido'] : null;
        $this->FechaEliminado = (!empty($data['FechaEliminado'])) ? $data['FechaEliminado'] : null;
        $this->FechaFinalizado = (!empty($data['FechaFinalizado'])) ? $data['FechaFinalizado'] : null;
        $this->FechaCaducado = (!empty($data['FechaCaducado'])) ? $data['FechaCaducado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
