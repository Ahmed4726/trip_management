<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestDocument extends Model
{
    protected $fillable = [
        'guest_id',
        'booking_guest_id',
        'booking_id',
        'document_type',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'uploaded_by',
        'uploaded_at',
        'notes',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function bookingGuest(): BelongsTo
    {
        return $this->belongsTo(BookingGuest::class, 'booking_guest_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Logic Helpers
    |--------------------------------------------------------------------------
    */

    // Linked to main guest profile
    public function isGuestDocument(): bool
    {
        return !is_null($this->guest_id)
            && is_null($this->booking_guest_id)
            && is_null($this->booking_id);
    }

    // Linked to booking-specific guest
    public function isBookingGuestDocument(): bool
    {
        return !is_null($this->booking_guest_id);
    }

    // Linked to booking (shared/general)
    public function isBookingDocument(): bool
    {
        return !is_null($this->booking_id)
            && is_null($this->booking_guest_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForGuest($query, $guestId)
    {
        return $query->where('guest_id', $guestId);
    }

    public function scopeForBookingGuest($query, $bookingGuestId)
    {
        return $query->where('booking_guest_id', $bookingGuestId);
    }

    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    // Flexible scope: get all relevant docs for a booking guest
    public function scopeRelevantForBookingGuest($query, $bookingGuestId, $bookingId = null, $guestId = null)
    {
        return $query->where(function ($q) use ($bookingGuestId, $bookingId, $guestId) {
            $q->where('booking_guest_id', $bookingGuestId);

            if ($bookingId) {
                $q->orWhere('booking_id', $bookingId);
            }

            if ($guestId) {
                $q->orWhere('guest_id', $guestId);
            }
        });
    }
}