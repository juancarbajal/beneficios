<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/10/15
 * Time: 07:50 PM
 */

namespace Application\Model;


class LayoutCampania
{
    public $id;
    public $BNF_Layout_id;
    public $BNF_Campanias_id;

    public $TipoLayout;
    public $fila;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Layout_id = (!empty($data['BNF_Layout_id'])) ? $data['BNF_Layout_id'] : null;
        $this->BNF_Campanias_id = (!empty($data['BNF_Campanias_id'])) ? $data['BNF_Campanias_id'] : null;
        $this->TipoLayout = (!empty($data['TipoLayout'])) ? $data['TipoLayout'] : null;
        $this->fila = (!empty($data['fila'])) ? $data['fila'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
