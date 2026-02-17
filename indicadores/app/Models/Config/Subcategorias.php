<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class Subcategorias extends Model
{
    protected $table = 'subcategorias';
    protected $fillable = [ 
        'nombre',
        'estado', 
        'id_categoria'
    ];

}
