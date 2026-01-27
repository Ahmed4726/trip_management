<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use Auditable;

    protected $fillable = [
        'slot_type',
        'status',
        'boat_id',
        'region_id',
        'departure_port_id',
        'arrival_port_id',
        'start_date',
        'end_date',
        'available_rooms',
        'notes',
        'created_from_template_id',
        'company_id',
        'duration_nights',
    ];

    protected $casts = [
        'available_rooms' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    public function boats()
    {
        return $this->belongsToMany(Boat::class, 'slot_boat');
    }


    public function template()
    {
        return $this->belongsTo(Template::class, 'created_from_template_id');
    }


    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function departurePort()
    {
        return $this->belongsTo(Port::class, 'departure_port_id');
    }

    public function arrivalPort()
    {
        return $this->belongsTo(Port::class, 'arrival_port_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isBlocked(): bool
    {
        return in_array($this->slot_type, ['Maintenance', 'Docking']);
    }
}
