<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Model;

class VariableValores extends Model
{
    protected $table = 'variable_valores';
    protected $fillable = [ 
        'ano',
        'mes',
        'valor',
        'id_usuario',
        'id_variable',
    ];

}
