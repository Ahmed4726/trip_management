<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boat extends Model
{
    use Auditable;

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

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'boat_booking');
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
