<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boat extends Model
{
      protected $fillable = [
        'name',
        'description',
        'max_capacity',
        'location',
        'status',
        'total_rooms',
    ];


    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

     public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function trip()
    {
        return $this->hasMany(Trip::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function booking()
    {
        return $this->hasManyThrough(Booking::class, Slot::class);
    }

    public function canBeDeleted(): bool
    {
        return $this->booking()->count() === 0;
    }
}
