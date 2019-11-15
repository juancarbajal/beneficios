<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/11/15
 * Time: 04:46 PM
 */

namespace Application\Model;


class BannersTienda
{
    public $id;
    public $BNF_Banners_id;
    public $Imagen;
    public $Url;
    public $Posicion;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Banners_id = (!empty($data['BNF_Banners_id'])) ? $data['BNF_Banners_id'] : null;
        $this->Imagen = (!empty($data['Imagen'])) ? $data['Imagen'] : null;
        $this->Url = (!empty($data['Url'])) ? $data['Url'] : null;
        $this->Posicion = (!empty($data['Posicion'])) ? $data['Posicion'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
