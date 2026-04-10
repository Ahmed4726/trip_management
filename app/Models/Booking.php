<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Booking extends Model
{
    use Auditable;

    protected $fillable = [
        'source',
        'agent_id',
        'notes',
        'company_id',
        'room_id',
        'boat_id',
        'slot_id',
        'guest_name',
        'status',
        'rate_plan_id',
        'payment_policy_id',
        'cancellation_policy_id',
        'price',
        'currency',
        'exchange_rate',
        'exchange_rate_timestamp',
        'salesperson_id',
        'deposit_amount',
        'deposit_due_date',
        'final_balance_due_date',
        'price_usd',
    ];



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

    public function boats()
    {
        return $this->belongsToMany(Boat::class);
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

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }

    public function guests()
    {
        return $this->belongsToMany(
            Guest::class,
            'booking_guest_room', // pivot table
            'booking_id',         // foreign key on pivot pointing to Booking
            'guest_id'            // foreign key on pivot pointing to Guest
        );
    }

    public function guestRoomAssignments()
    {
        return $this->hasMany(BookingGuestRoom::class);
    }

        public function installments()
    {
        return $this->hasMany(BookingInstallment::class);
    }

    public function payments()
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function getAmountPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceDueAttribute()
    {
        return $this->price_usd - $this->amount_paid;
    }

}
