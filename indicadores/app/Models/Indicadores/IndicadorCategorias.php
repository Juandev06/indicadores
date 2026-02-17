<?php

namespace App\Models\Indicadores;

use Illuminate\Database\Eloquent\Model;

class IndicadorCategorias extends Model
{
    protected $table = 'indicador_categorias';
    protected $fillable = [ 
        'id_indicador', 
        'id_categoria' , 
        'id_subcategoria'
    ];

}
