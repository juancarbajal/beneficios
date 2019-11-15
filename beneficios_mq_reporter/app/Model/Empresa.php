<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'BNF_Empresa';

    public function getEmpresa($id_empresa){
        $id = (int) $id_empresa;

        $result = $this::find($id);

        return $result;

    }

}
