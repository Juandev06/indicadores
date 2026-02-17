<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class set_values_goals_indicators extends Model
{
    use HasFactory;
    protected $fillable = [ 'id_indicador', 'mes', 'value', 'formule_id' ];

}
