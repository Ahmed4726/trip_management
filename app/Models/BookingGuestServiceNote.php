<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestServiceNote extends Model
{
    protected $fillable = [
        'booking_guest_id',
        'vip_level',
        'celebration_type',
        'celebration_notes',
        'beach_dining_requested',
        'excursion_requests',
        'activity_preferences',
        'guest_handling_notes',
        'internal_service_notes',
    ];

    protected $casts = [
        'beach_dining_requested' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function bookingGuest(): BelongsTo
    {
        return $this->belongsTo(BookingGuest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Logic Helpers
    |--------------------------------------------------------------------------
    */

    public function isVip(): bool
    {
        return !empty($this->vip_level);
    }

    public function hasCelebration(): bool
    {
        return !empty($this->celebration_type);
    }

    public function wantsBeachDining(): bool
    {
        return $this->beach_dining_requested;
    }

    public function hasExcursionRequests(): bool
    {
        return !empty($this->excursion_requests);
    }

    public function hasActivityPreferences(): bool
    {
        return !empty($this->activity_preferences);
    }

    public function hasSpecialHandlingNotes(): bool
    {
        return !empty($this->guest_handling_notes);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeVip($query)
    {
        return $query->whereNotNull('vip_level');
    }

    public function scopeWithCelebrations($query)
    {
        return $query->whereNotNull('celebration_type');
    }

    public function scopeBeachDining($query)
    {
        return $query->where('beach_dining_requested', true);
    }
}