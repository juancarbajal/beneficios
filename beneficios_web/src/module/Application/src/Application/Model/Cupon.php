<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 28/10/15
 * Time: 18:46
 */

namespace Application\Model;

class Cupon
{
    public $id;
    public $BNF_OfertaEmpresaCliente_id;
    public $BNF_Empresa_id;
    public $BNF_Cliente_id;
    public $EstadoCupon;
    public $BNF_Oferta_id;
    public $BNF_Oferta_Atributo_id;
    public $CodigoCupon;
    public $BNF_Categoria_id;
    public $BNF_ClienteCorreo_id;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_OfertaEmpresaCliente_id = (
        !empty($data['BNF_OfertaEmpresaCliente_id'])) ? $data['BNF_OfertaEmpresaCliente_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->EstadoCupon = (!empty($data['EstadoCupon'])) ? $data['EstadoCupon'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Oferta_Atributo_id = (!empty($data['BNF_Oferta_Atributo_id'])) ? $data['BNF_Oferta_Atributo_id'] : null;
        $this->CodigoCupon = (!empty($data['CodigoCupon'])) ? $data['CodigoCupon'] : null;
        $this->BNF_Categoria_id = (!empty($data['BNF_Categoria_id'])) ? $data['BNF_Categoria_id'] : null;
        $this->BNF_ClienteCorreo_id = (!empty($data['BNF_ClienteCorreo_id'])) ? $data['BNF_ClienteCorreo_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
