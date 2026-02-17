<?php

namespace App\Models\Metas;

use Illuminate\Database\Eloquent\Model;
/**
 * Metas de indicadores
 * @property int anno de la meta
 * @property double value valor de la meta 
 * @property int id_indicador id del indicador (tabla indicadores) 
 * @property int mes mes del indicador
 * @property int id_periodo id del periodo (tabla aux_periodo_dets)
 */
class Metas extends Model
{
    protected $table = 'metas';
    protected $fillable = [ 
        'anno',
        'mes',
        'value',
        'id_indicador',
        'id_periodo',
    ];

}
