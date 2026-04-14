<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestFoodPreference extends Model
{
    protected $fillable = [
        'booking_guest_id',
        'dietary_type',
        'allergy_flag',
        'allergy_details',
        'dislikes',
        'favorite_foods',
        'breakfast_preference',
        'snack_preference',
        'food_notes',
        'lactose_intolerant',
        'gluten_free',
        'halal',
        'vegetarian',
        'vegan',
        'pescatarian',
        'kosher',
    ];

    protected $casts = [
        'allergy_flag' => 'boolean',
        'lactose_intolerant' => 'boolean',
        'gluten_free' => 'boolean',
        'halal' => 'boolean',
        'vegetarian' => 'boolean',
        'vegan' => 'boolean',
        'pescatarian' => 'boolean',
        'kosher' => 'boolean',
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

    public function hasAllergies(): bool
    {
        return $this->allergy_flag && !empty($this->allergy_details);
    }

    public function hasDietaryRestrictions(): bool
    {
        return $this->lactose_intolerant ||
               $this->gluten_free ||
               $this->vegetarian ||
               $this->vegan ||
               $this->pescatarian ||
               $this->kosher ||
               $this->halal;
    }

    public function isHalal(): bool
    {
        return $this->halal;
    }

    public function isVegetarian(): bool
    {
        return $this->vegetarian;
    }

    public function isVegan(): bool
    {
        return $this->vegan;
    }

    public function isGlutenFree(): bool
    {
        return $this->gluten_free;
    }

    public function isLactoseIntolerant(): bool
    {
        return $this->lactose_intolerant;
    }

    public function getDietLabels(): array
    {
        $labels = [];

        if ($this->halal) $labels[] = 'Halal';
        if ($this->vegetarian) $labels[] = 'Vegetarian';
        if ($this->vegan) $labels[] = 'Vegan';
        if ($this->pescatarian) $labels[] = 'Pescatarian';
        if ($this->kosher) $labels[] = 'Kosher';
        if ($this->gluten_free) $labels[] = 'Gluten Free';
        if ($this->lactose_intolerant) $labels[] = 'Lactose Intolerant';

        if ($this->dietary_type) {
            $labels[] = ucfirst($this->dietary_type);
        }

        return $labels;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeWithAllergies($query)
    {
        return $query->where('allergy_flag', true);
    }

    public function scopeHalal($query)
    {
        return $query->where('halal', true);
    }

    public function scopeVegetarian($query)
    {
        return $query->where('vegetarian', true);
    }

    public function scopeVegan($query)
    {
        return $query->where('vegan', true);
    }

    public function scopeWithRestrictions($query)
    {
        return $query->where(function ($q) {
            $q->where('lactose_intolerant', true)
              ->orWhere('gluten_free', true)
              ->orWhere('vegetarian', true)
              ->orWhere('vegan', true)
              ->orWhere('pescatarian', true)
              ->orWhere('kosher', true)
              ->orWhere('halal', true);
        });
    }
}