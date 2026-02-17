<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Model;

/**
 * periodo habilitado para ingreso de información
 */
class VariablePeriodoHabilitado extends Model
{
    protected $table = 'variable_periodo_habilitado';
    public $timestamps = false;
    protected $fillable = [
        'ano',
        'mes',
        'estado', // A: activo, I: inactivo
        'fecha_activacion',
        'id_usuario_activacion',
        'fecha_inactivacion',
        'id_usuario_inactivacion',
    ];
}
