<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnablePeriodicities extends Model
{
    use HasFactory;
    protected $fillable = [ 'id_periodo','mes', 'is_enable'];

}
