<?php

namespace App\Models;

use App\Http\Controllers\BookingGuestFoodPreferencesController;
use Illuminate\Database\Eloquent\Model;

class BookingGuest extends Model
{
    protected $table = 'booking_guest';

    protected $fillable = [
        'booking_id',
        'guest_id',
        'room_id',
        'boat_id',
        'is_lead_guest',
        'is_group_leader',
        'is_child',
        'room_assignment_status',
        'guest_status',
        'passport_received',
        'travel_details_completed',
        'medical_completed',
        'fnb_completed',
        'documents_completed',
        'operational_notes',
        'arrival_shared_group',
        'departure_shared_group',
        'completion_score',
        'check_in_date',
        'check_out_date',
    ];

    public function bookings()
    {
        return $this->belongsTo(Booking::class);
    }

    public function rooms()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function travelDetails()
    {
        return $this->hasMany(BookingTravelDetail::class);
    }

    public function documents()
    {
        return $this->hasMany(GuestDocument::class);
    }

    public function medical()
    {
        return $this->hasOne(BookingGuestMedical::class);
    }

    public function foodPreference()
    {
        return $this->hasOne(BookingGuestFoodPreferences::class);
    }

    public function drinkPreference()
    {
        return $this->hasOne(BookingGuestDrinkPreferences::class);
    }

    public function housekeeping()
    {
        return $this->hasOne(BookingGuestHousekeepings::class);
    }

    public function serviceNote()
    {
        return $this->hasOne(BookingGuestServiceNote::class);
    }

    public function diving()
    {
        return $this->hasOne(BookingGuestDiving::class);
    }

    public function surfing()
    {
        return $this->hasOne(BookingGuestSurfing::class);
    }
}
