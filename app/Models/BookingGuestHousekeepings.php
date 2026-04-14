<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestHousekeeping extends Model
{
    protected $fillable = [
        'booking_guest_id',
        'room_id',
        'bed_setup_preference',
        'pillow_preference',
        'towel_change_preference',
        'bathroom_assignment_notes',
        'cleaning_notes',
        'baby_cot_required',
        'umbrella_required',
        'beach_setup_required',
        'room_comfort_notes',
    ];

    protected $casts = [
        'baby_cot_required' => 'boolean',
        'umbrella_required' => 'boolean',
        'beach_setup_required' => 'boolean',
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

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Logic Helpers
    |--------------------------------------------------------------------------
    */

    public function needsBabyCot(): bool
    {
        return $this->baby_cot_required;
    }

    public function needsUmbrella(): bool
    {
        return $this->umbrella_required;
    }

    public function needsBeachSetup(): bool
    {
        return $this->beach_setup_required;
    }

    public function prefersTwin(): bool
    {
        return $this->bed_setup_preference === 'twin';
    }

    public function prefersDouble(): bool
    {
        return $this->bed_setup_preference === 'double';
    }

    public function hasSpecialCleaningNotes(): bool
    {
        return !empty($this->cleaning_notes);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeNeedsBabyCot($query)
    {
        return $query->where('baby_cot_required', true);
    }

    public function scopeNeedsBeachSetup($query)
    {
        return $query->where('beach_setup_required', true);
    }

    public function scopeWithRoom($query)
    {
        return $query->whereNotNull('room_id');
    }
}