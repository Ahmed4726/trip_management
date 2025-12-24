<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boat extends Model
{
      protected $fillable = [
        'name',
        'description',
        'location',
        'status',
    ];
     public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function trip()
    {
        return $this->hasMany(Trip::class);
    }
}
