<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{
    protected $table = 'BNF_Rubro';

    public function fetchAll()
    {
        $query = "SELECT * FROM BNF_Rubro WHERE Eliminado = 0";

        return \DB::select($query);
    }

}
