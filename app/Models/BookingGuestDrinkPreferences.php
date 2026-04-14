<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestDrinkPreference extends Model
{
    protected $fillable = [
        'booking_guest_id',
        'drinks_alcohol',
        'wine_preference',
        'spirits_preference',
        'cocktail_preference',
        'beer_preference',
        'coffee_preference',
        'tea_preference',
        'soft_drink_preference',
        'water_preference',
        'drink_notes',
    ];

    protected $casts = [
        'drinks_alcohol' => 'boolean',
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

    public function drinksAlcohol(): bool
    {
        return $this->drinks_alcohol;
    }

    public function hasWinePreference(): bool
    {
        return !empty($this->wine_preference);
    }

    public function hasSpiritsPreference(): bool
    {
        return !empty($this->spirits_preference);
    }

    public function hasCocktailPreference(): bool
    {
        return !empty($this->cocktail_preference);
    }

    public function hasBeerPreference(): bool
    {
        return !empty($this->beer_preference);
    }

    public function hasCoffeePreference(): bool
    {
        return !empty($this->coffee_preference);
    }

    public function hasTeaPreference(): bool
    {
        return !empty($this->tea_preference);
    }

    public function hasSoftDrinkPreference(): bool
    {
        return !empty($this->soft_drink_preference);
    }

    public function hasWaterPreference(): bool
    {
        return !empty($this->water_preference);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDrinksAlcohol($query)
    {
        return $query->where('drinks_alcohol', true);
    }

    public function scopeNonAlcoholic($query)
    {
        return $query->where('drinks_alcohol', false);
    }

    public function scopeCoffeeLovers($query)
    {
        return $query->whereNotNull('coffee_preference');
    }

    public function scopeTeaLovers($query)
    {
        return $query->whereNotNull('tea_preference');
    }
}