<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use Auditable;

    protected $fillable = [
        'name', 'gender', 'trip_id', 'email', 'dob', 'passport', 'nationality', 'cabin', 'surfLevel', 'boardDetails',
        'arrivalFlightDate', 'arrivalFlightNumber', 'arrivalAirport', 'arrivalTime', 'hotelPickup',
        'departureFlightDate', 'departureFlightNumber', 'departureAirport', 'departureTime',
        'medicalDietary', 'specialRequests', 'insuranceName', 'policyNumber',
        'emergencyName', 'emergencyRelation', 'emergencyPhone',
        'guestWhatsapp', 'guestEmail',
        'image_path', 'pdf_path', 'video_path','company_id','phone','address'

    ];

    // public function otherGuests()
    // {
    //     return $this->hasMany(OtherGuest::class);
    // }

    // Guest.php
public function otherGuests()
{
    return $this->hasMany(OtherGuest::class, 'guest_id');
}
   public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

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
}

