<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/11/15
 * Time: 06:33 PM
 */

namespace Oferta\Model;

class OfertaFormulario
{
    public $id;
    public $BNF_Oferta_id;
    public $BNF_Formulario_id;
    public $Descripcion;
    public $Activo;
    public $Requerido;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->BNF_Formulario_id = (!empty($data['BNF_Formulario_id'])) ? $data['BNF_Formulario_id'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->Activo = (!empty($data['Activo'])) ? $data['Activo'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Requerido = (!empty($data['Requerido'])) ? $data['Requerido'] : null;

        $this->valor = (!empty($data['valor'])) ? $data['valor'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
