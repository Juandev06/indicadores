<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuesFormules extends Model
{
    use HasFactory;
    protected $fillable = [ 
        'formule_id', 
        'value'
    ];
}
