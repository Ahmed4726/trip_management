<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestSurfing extends Model
{
    protected $fillable = [
        'booking_guest_id',
        'is_surfer',
        'surf_level',
        'bringing_own_board',
        'board_count',
        'board_type',
        'board_length',
        'board_width',
        'board_volume',
        'rental_required',
        'coaching_required',
        'photo_video_interest',
        'surfing_notes',
    ];

    protected $casts = [
        'is_surfer' => 'boolean',
        'bringing_own_board' => 'boolean',
        'rental_required' => 'boolean',
        'coaching_required' => 'boolean',
        'photo_video_interest' => 'boolean',
        'board_count' => 'integer',
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

    public function isBeginner(): bool
    {
        return $this->surf_level === 'beginner';
    }

    public function isAdvanced(): bool
    {
        return in_array($this->surf_level, ['advanced', 'professional']);
    }

    public function bringsOwnBoard(): bool
    {
        return $this->bringing_own_board;
    }

    public function needsRental(): bool
    {
        return $this->rental_required;
    }

    public function needsCoaching(): bool
    {
        return $this->coaching_required;
    }

    public function wantsMedia(): bool
    {
        return $this->photo_video_interest;
    }

    public function hasBoardDetails(): bool
    {
        return !empty($this->board_type) || !empty($this->board_length);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSurfers($query)
    {
        return $query->where('is_surfer', true);
    }

    public function scopeBeginners($query)
    {
        return $query->where('surf_level', 'beginner');
    }

    public function scopeAdvanced($query)
    {
        return $query->whereIn('surf_level', ['advanced', 'professional']);
    }

    public function scopeNeedsCoaching($query)
    {
        return $query->where('coaching_required', true);
    }

    public function scopeNeedsRental($query)
    {
        return $query->where('rental_required', true);
    }
}