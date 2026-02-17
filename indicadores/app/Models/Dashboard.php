<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $table = 'dashboard';

    protected $fillable = [ 
        'order',
        'id_indicator',  // id del indicador
        'id_usuario',    // id del usuario
        'chart_type',    // tipo de grafico: line (default), bar, pie, doughnut
        'show_detail',   // mostrar el detalle de variables del indicador: false (default), true
    ];

}
