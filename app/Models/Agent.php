<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}

