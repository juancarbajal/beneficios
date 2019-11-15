<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'BNF_Categoria';

    public function getCategoriaIds()
    {
        $query = "SELECT * FROM BNF_Categoria WHERE Eliminado = 0 ORDER BY id";

        return \DB::select($query);
    }

    public function fetchAll()
    {
        $query = "SELECT * FROM BNF_Categoria WHERE Eliminado = 0";

        return \DB::select($query);
    }
}
