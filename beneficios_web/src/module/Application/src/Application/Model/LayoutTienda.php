<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 28/10/15
 * Time: 03:47 PM
 */

namespace Application\Model;


class LayoutTienda
{
    public $id;
    public $BNF_Layout_id;

    public $TipoLayout;
    public $fila;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Layout_id = (!empty($data['BNF_Layout_id'])) ? $data['BNF_Layout_id'] : null;
        $this->TipoLayout = (!empty($data['TipoLayout'])) ? $data['TipoLayout'] : null;
        $this->fila = (!empty($data['fila'])) ? $data['fila'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
