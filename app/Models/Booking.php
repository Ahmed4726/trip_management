<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'trip_id',
        'customer_name',
        'guests',
        'source',
        'email',
        'phone_number',
        'nationality',
        'passport_number',
        'booking_status',
        'pickup_location_time',
        'addons',
        'room_preference',
        'agent_id',
        'comments',
        'notes',
        'token',
        'company_id',
        'dp_paid',
        'room_id',
        'boat_id'
    ];


       /**
     * Get the trip that this booking belongs to.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    /**
     * Get the agent assigned to this booking.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }


    public function rooms()
    {
        return $this->belongsTo(Room::class,'room_id');
    }

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }
}
