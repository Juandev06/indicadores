<?php

namespace App\Models\Aux;

use Illuminate\Database\Eloquent\Model;

class Periodos extends Model
{
    protected $table = 'aux_periodos';
    public $timestamps = false;
    protected $fillable = [ 
        'nombre', 
        'estado',
        'cant_meses',
     ];
}
