<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestMedical extends Model
{
    protected $fillable = [
        'booking_guest_id',
        'medical_conditions',
        'medications',
        'food_allergy_flag',
        'food_allergy_details',
        'other_allergies',
        'motion_sickness',
        'physical_limitations',
        'mobility_notes',
        'special_assistance_required',
        'assistance_notes',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_email',
    ];

    protected $casts = [
        'food_allergy_flag' => 'boolean',
        'motion_sickness' => 'boolean',
        'special_assistance_required' => 'boolean',
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

    public function hasFoodAllergy(): bool
    {
        return $this->food_allergy_flag && !empty($this->food_allergy_details);
    }

    public function hasOtherAllergies(): bool
    {
        return !empty($this->other_allergies);
    }

    public function hasAnyAllergy(): bool
    {
        return $this->hasFoodAllergy() || $this->hasOtherAllergies();
    }

    public function hasMedicalConditions(): bool
    {
        return !empty($this->medical_conditions);
    }

    public function hasMedications(): bool
    {
        return !empty($this->medications);
    }

    public function needsSpecialAssistance(): bool
    {
        return $this->special_assistance_required;
    }

    public function hasMobilityIssues(): bool
    {
        return !empty($this->physical_limitations) || !empty($this->mobility_notes);
    }

    public function hasEmergencyContact(): bool
    {
        return !empty($this->emergency_contact_name) ||
               !empty($this->emergency_contact_phone) ||
               !empty($this->emergency_contact_email);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeWithAllergies($query)
    {
        return $query->where(function ($q) {
            $q->where('food_allergy_flag', true)
              ->orWhereNotNull('other_allergies');
        });
    }

    public function scopeNeedingAssistance($query)
    {
        return $query->where('special_assistance_required', true);
    }

    public function scopeWithMedicalConditions($query)
    {
        return $query->whereNotNull('medical_conditions');
    }
}