<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGuestRoom extends Model
{
    protected $table = 'booking_guest_room';

    protected $fillable = [
        'booking_id',
        'guest_id',
        'room_id',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
