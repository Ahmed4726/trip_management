<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
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
}

