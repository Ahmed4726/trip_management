<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use Auditable;

    protected $fillable = [
        'gender', 'email', 'dob', 'passport',
        'company_id','phone','address','first_name','last_name'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function roomAssignments()
    {
        return $this->hasMany(BookingGuestRoom::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'booking_guest_room')
                    ->withPivot('booking_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(
            Booking::class,
            'booking_guest_room', // pivot table
            'guest_id',           // foreign key on pivot pointing to Guest
            'booking_id'          // foreign key on pivot pointing to Booking
        );
    }

    // Computed full name (VERY useful everywhere)
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}

