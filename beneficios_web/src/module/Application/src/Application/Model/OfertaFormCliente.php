<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 11/11/15
 * Time: 07:22 PM
 */
namespace Application\Model;

class OfertaFormCliente
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Cliente_id;
    public $BNF_Empresa_id;
    public $FechaCreacion;
    public $BNF_Categoria_id;
    public $FechaActualizacion;

    public $BNF_Oferta_Atributo_id;
    public $BNF_OfertaEmpresaCliente_id;


    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_Oferta_Atributo_id = (!empty($data['BNF_Oferta_Atributo_id'])) ? $data['BNF_Oferta_Atributo_id'] : null;
        $this->BNF_OfertaEmpresaCliente_id = (!empty($data['BNF_OfertaEmpresaCliente_id'])) ? $data['BNF_OfertaEmpresaCliente_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->BNF_Categoria_id = (!empty($data['BNF_Categoria_id'])) ? $data['BNF_Categoria_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}