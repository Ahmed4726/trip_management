<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTravelDetail extends Model
{
    protected $fillable = [
        'booking_id',
        'booking_guest_id',
        'direction',
        'travel_type',
        'date',
        'time',
        'airport',
        'airline',
        'flight_number',
        'pickup_required',
        'dropoff_required',
        'location_name',
        'location_address',
        'notes',
        'applies_to_all',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'pickup_required' => 'boolean',
        'dropoff_required' => 'boolean',
        'applies_to_all' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(BookingGuest::class, 'booking_guest_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Logic Helpers
    |--------------------------------------------------------------------------
    */

    // Booking-wide record
    public function isBookingWide(): bool
    {
        return is_null($this->booking_guest_id) && $this->applies_to_all;
    }

    // Guest-specific record
    public function isGuestSpecific(): bool
    {
        return !is_null($this->booking_guest_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    // Scope: booking-wide only
    public function scopeBookingWide($query)
    {
        return $query->whereNull('booking_guest_id')
                     ->where('applies_to_all', true);
    }

    // Scope: guest-specific only
    public function scopeGuestSpecific($query)
    {
        return $query->whereNotNull('booking_guest_id');
    }

    // Scope: for a specific guest (including shared if needed)
    public function scopeForGuest($query, $guestId, $includeShared = true)
    {
        return $query->where(function ($q) use ($guestId, $includeShared) {
            $q->where('booking_guest_id', $guestId);

            if ($includeShared) {
                $q->orWhere(function ($sub) {
                    $sub->whereNull('booking_guest_id')
                        ->where('applies_to_all', true);
                });
            }
        });
    }
}