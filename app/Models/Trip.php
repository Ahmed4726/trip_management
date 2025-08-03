<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'title',
        'region',
        'status',
        'leading_guest_id',
        'notes',
        'trip_type',
        'start_date',
        'end_date',
        'guests',
        'price',
        'boat',
        'agent_id',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

       public function leadingGuest()
    {
        return $this->belongsTo(Guest::class, 'leading_guest_id');
    }
}

