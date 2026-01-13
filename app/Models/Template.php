<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'product_name', 'product_type', 'region_id', 'vessels_allowed',
        'duration_days', 'duration_nights', 'departure_ports', 'arrival_ports',
        'min_bookings', 'default_checkin_from', 'default_checkin_to',
        'default_checkout_from', 'default_checkout_to',
        'inclusions', 'exclusions', 'obligatory_surcharges',
        'experience_level', 'requirements_description',
        'public_comment', 'internal_comment'
    ];

    protected $casts = [
        'vessels_allowed' => 'array',
        'departure_ports' => 'array',
        'arrival_ports' => 'array',
    ];

    public function region() {
        return $this->belongsTo(Region::class);
    }

    public function vessels_allowed_names() {
        return \App\Models\Boat::whereIn('id', $this->vessels_allowed)->pluck('name')->toArray();
    }

    public function boats()
    {
        return $this->belongsTo(Boat::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class, 'created_from_template_id');
    }

}
