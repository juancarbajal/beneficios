<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Hijos extends Model
{
    protected $table = 'BNF_DM_Dim_Hijos';
    

    public function fetchAll()
    {
        $query = "SELECT * FROM BNF_DM_Dim_Hijos";

        return \DB::select($query);
    }
}
