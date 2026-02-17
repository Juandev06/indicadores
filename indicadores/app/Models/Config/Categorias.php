<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $table = 'categorias';
    protected $fillable = [ 
        'nombre',
        'estado'
    ];

}
