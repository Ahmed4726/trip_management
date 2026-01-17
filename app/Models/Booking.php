<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Booking extends Model
{
    use Auditable;
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
        'boat_id',
        'slot_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'status',
        'rate_plan_id',
        'payment_policy_id',
        'cancellation_policy_id',
        'pricing_snapshot_json',
        'payment_policy_snapshot_json',
        'cancellation_policy_snapshot_json',
        'terms_snapshot',
        'price',
        'currency',
        'salesperson_id',

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


    public function room()
    {
        return $this->belongsTo(Room::class,'room_id');
    }

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
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

        // ✅ ROOMS
    public function rooms()
    {
        return $this->belongsToMany(
            Room::class,
            'booking_rooms'   // pivot table
        );
    }

        // ✅ GUESTS
    public function guests()
    {
        return $this->belongsToMany(
            Guest::class,
            'booking_guests'   // pivot table
        );
    }
}
