<?php

namespace App\Models\Indicadores;

use Illuminate\Database\Eloquent\Model;

/**
 * Valores calculados de los indicadores para cada periodo 
 */
class IndicadorValores extends Model
{
    protected $table = 'indicador_valores';
    protected $fillable = [
        'ano',
        'mes',
        'valor', 
        'id_indicador',
        'id_usuario',
        'obs',
    ];
}
