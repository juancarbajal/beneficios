<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 04/11/15
 * Time: 07:49 PM
 */

namespace Application\Model;


class DetalleOfertaFormulario
{
    public $BNF_OfertaFormulario_id;
    public $Descripcion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->BNF_OfertaFormulario_id =
            (!empty($data['BNF_OfertaFormulario_id'])) ? $data['BNF_OfertaFormulario_id'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
