<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Model;

class Variables extends Model
{
    protected $table = 'variables';
    protected $fillable = [
        'nombre',
        'estado',
        'obs',
        'tipo',
        'id_periodo',
        'id_calendario', // 1: fiscal, 2: tarifario
        'id_area',
        'id_usuario'
    ];
}
