<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use Auditable;

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
        return $this->belongsToMany(Booking::class);
    }

    public function guestAssignments()
    {
        return $this->hasMany(BookingGuestRoom::class);
    }

    public function guests()
    {
        return $this->belongsToMany(Guest::class, 'booking_guest_room')
                    ->withPivot('booking_id');
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
