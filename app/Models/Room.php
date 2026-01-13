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
        'deck',
        'bed_type',
        'extra_beds',
    ];
    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    public function slots()
    {
        return $this->belongsToMany(Slot::class);
    }

    public function booking()
    {
        return $this->hasManyThrough(Booking::class, Slot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function trips()
    {
        return $this->belongsTo(Trip::class);
    }

    public function maxCapacity()
    {
        return $this->capacity + $this->extra_beds;
    }

    public function canBeDeleted(): bool
    {
        return $this->booking()->count() === 0;
    }
}
