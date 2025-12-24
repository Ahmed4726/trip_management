<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'boat_id',
        'room_name',
        'capacity',
        'price_per_night',
        'status',
    ];
     public function boat()
    {
        return $this->belongsTo(Boat::class);
    }
}
