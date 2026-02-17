<?php

namespace App\Models\Metas;

use Illuminate\Database\Eloquent\Model;

/**
 * periodo habilitado para ingreso de metas
 */
class MetaPeriodoHabilitado extends Model
{
    protected $table = 'meta_periodo_habilitado';
    public $timestamps = false;
    protected $fillable = [
        'ano',
        'estado', // A: activo, I: inactivo
        'fecha_activacion',
        'id_usuario_activacion',
        'obs_activacion',
        'fecha_inactivacion',
        'id_usuario_inactivacion',
        'obs_inactivacion',
    ];
}
