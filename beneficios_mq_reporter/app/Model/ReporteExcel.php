<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReporteExcel extends Model
{
    protected $table = 'BNF_ReporteExcel';

    public $timestamps = true;

    protected $fillable = [
        'name'
    ];
}
