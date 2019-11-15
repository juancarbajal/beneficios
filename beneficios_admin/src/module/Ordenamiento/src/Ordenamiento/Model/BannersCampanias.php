<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/10/15
 * Time: 04:10 PM
 */

namespace Ordenamiento\Model;

class BannersCampanias
{
    public $id;
    public $BNF_Banners_id;
    public $BNF_Campanias_id;
    public $Imagen;
    public $Url;
    public $Posicion;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $BNF_Empresa_id;
    public $Eliminado;

    public $NombreBanner;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Banners_id = (!empty($data['BNF_Banners_id'])) ? $data['BNF_Banners_id'] : null;
        $this->BNF_Campanias_id = (!empty($data['BNF_Campanias_id'])) ? $data['BNF_Campanias_id'] : null;
        $this->Imagen = (!empty($data['Imagen'])) ? $data['Imagen'] : null;
        $this->Url = (!empty($data['Url'])) ? $data['Url'] : null;
        $this->Posicion = (!empty($data['Posicion'])) ? $data['Posicion'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;

        $this->NombreBanner = (!empty($data['NombreBanner'])) ? $data['NombreBanner'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
