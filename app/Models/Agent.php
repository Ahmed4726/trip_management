<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'commission',
        'phone',
        'company'
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function trip()
    {
        return $this->belongsToMany(Trip::class);
    }

    // public function agents()
    // {
    //     return $this->belongsToMany(Booking::class);
    // }

}

