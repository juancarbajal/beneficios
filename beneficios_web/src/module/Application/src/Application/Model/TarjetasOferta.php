<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 13/04/16
 * Time: 10:11 AM
 */

namespace Application\Model;

class TarjetasOferta
{
    public $id;
    public $BNF_Tarjetas_id;
    public $BNF_Oferta_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Descripcion;
    public $Imagen;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Tarjetas_id = (!empty($data['BNF_Tarjetas_id'])) ? $data['BNF_Tarjetas_id'] : null;
        $this->BNF_Oferta_id = (!empty($data['BNF_Oferta_id'])) ? $data['BNF_Oferta_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->Imagen = (!empty($data['Imagen'])) ? $data['Imagen'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
