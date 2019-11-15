<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'BNF_DM_Dim_EstadoCivil';
    

    public function fetchAll()
    {
        $query = "SELECT * FROM BNF_DM_Dim_EstadoCivil";

        return \DB::select($query);
    }
}
