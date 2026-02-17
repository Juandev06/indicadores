<?php

namespace App\Models\Indicadores;

use Illuminate\Database\Eloquent\Model;

class Indicadores extends Model
{
    protected $table = 'indicadores';
    protected $fillable = [
        'nombre',
        'estado',
        'tipo', // N: numérico, P: porcentual
        'id_periodo',
        'id_calendario', // 1: fiscal, 2: tarifario
        'id_usuario',
        'formula',
        'tolerancia', // margen de toleracia con respecto a la meta (%) (valor por defecto .env(TOLERANCIA_INDICADORES)
        'tendencia', // 1: creciente, 2: decreciente (valor por defecto: 1)
        'ficha_tec_archivo', // nombre del archivo de ficha técnica
        'ficha_tec_carpeta', // carpeta del archivo de ficha técnica
        'ext', // extensión del archivo de ficha técnica
        'mimetype', // 
        'size', //
    ];
}
