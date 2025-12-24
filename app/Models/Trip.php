<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'title',
        'region',
        'status',
        'leading_guest_id',
        'notes',
        'trip_type',
        'start_date',
        'end_date',
        'guests',
        'price',
        'boat_id',
        'agent_id',
        'guest_form_token',
        'guest_form_url',
        'company_id',
        'balance_due_date',
        'rate_plan_id',
        'payment_policy_id',
        'cancellation_policy_id',
        'pricing_snapshot_json',
        'payment_policy_snapshot_json',
        'cancellation_policy_snapshot_json',
        'dp_amount',
        
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

       public function leadingGuest()
    {
        return $this->belongsTo(Guest::class, 'leading_guest_id');
    }
    // Trip.php
    public function guestList()
    {
        return $this->hasMany(Guest::class);
    }

    // Guest.php
    public function otherGuests()
    {
        return $this->hasMany(OtherGuest::class);
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class);
    }

    //booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratePlan()
    { 
        return $this->belongsTo(RatePlan::class); 
    }
    public function paymentPolicy()
    { 
        return $this->belongsTo(PaymentPolicy::class); 
    }
    public function cancellationPolicy()
    { 
        return $this->belongsTo(CancellationPolicy::class); 
    }

    public function company()
    { 
        return $this->belongsTo(Company::class); 
    }

    public function boat()
    { 
        return $this->belongsTo(Boat::class); 
    }

}

