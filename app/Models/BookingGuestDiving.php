<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestDiving extends Model
{
    protected $fillable = [
        'booking_guest_id',
        'is_diver',
        'certification_agency',
        'certification_level',
        'certification_number',
        'logged_dives',
        'last_dive_date',
        'dive_insurance',
        'insurance_provider',
        'equipment_rental_required',
        'wetsuit_size',
        'fin_size',
        'bcd_size',
        'diving_medical_notes',
        'diving_notes',
    ];

    protected $casts = [
        'is_diver' => 'boolean',
        'dive_insurance' => 'boolean',
        'equipment_rental_required' => 'boolean',
        'last_dive_date' => 'date',
        'logged_dives' => 'integer',
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

    public function isCertified(): bool
    {
        return !empty($this->certification_level);
    }

    public function hasInsurance(): bool
    {
        return $this->dive_insurance;
    }

    public function needsEquipmentRental(): bool
    {
        return $this->equipment_rental_required;
    }

    public function isActiveDiver(): bool
    {
        return $this->is_diver && $this->logged_dives > 0;
    }

    public function needsMedicalReview(): bool
    {
        return !empty($this->diving_medical_notes);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDivers($query)
    {
        return $query->where('is_diver', true);
    }

    public function scopeWithInsurance($query)
    {
        return $query->where('dive_insurance', true);
    }

    public function scopeNeedsEquipment($query)
    {
        return $query->where('equipment_rental_required', true);
    }

    public function scopeCertified($query)
    {
        return $query->whereNotNull('certification_level');
    }
}