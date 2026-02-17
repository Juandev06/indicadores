<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [ 
        'name', 
        'status' 
    ];

    public function  users(){
        return $this->hasMany(User::class);
    }
}


