<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 15/10/15
 * Time: 19:18
 */

namespace Application\Model;

class LayoutCategoria
{
    public $id;
    public $BNF_Layout_id;
    public $BNF_Categoria_id;
    public $TipoLayout;
    public $fila;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Layout_id = (!empty($data['BNF_Layout_id'])) ? $data['BNF_Layout_id'] : null;
        $this->BNF_Categoria_id = (!empty($data['BNF_Categoria_id'])) ? $data['BNF_Categoria_id'] : null;
        $this->TipoLayout = (!empty($data['TipoLayout'])) ? $data['TipoLayout'] : null;
        $this->fila = (!empty($data['fila'])) ? $data['fila'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
