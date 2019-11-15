<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/06/16
 * Time: 05:56 PM
 */

namespace Demanda\Model;

class DemandaPremiosSegmentos
{
    public $id;
    public $BNF3_Segmento_id;
    public $BNF3_Demanda_id;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF3_Segmento_id = (!empty($data['BNF3_Segmento_id'])) ? $data['BNF3_Segmento_id'] : null;
        $this->BNF3_Demanda_id = (!empty($data['BNF3_Demanda_id'])) ? $data['BNF3_Demanda_id'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
