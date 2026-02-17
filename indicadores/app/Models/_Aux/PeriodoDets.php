<?php

namespace App\Models\Aux;

use Illuminate\Database\Eloquent\Model;
/**
 * Detalles de periodos
 */
class PeriodoDets extends Model
{
    protected $table = 'aux_periodo_dets';
    public $timestamps = false;
    protected $fillable = [ 
        'id_periodo', 
        'mes',
        'aplica',
        'nombre',
        'id_calendario'
    ];
}
