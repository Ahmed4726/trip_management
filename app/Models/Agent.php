<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'commission',
        'phone',
        'company_id'
    ];
    public function slots()
    {
        return $this->belongsToMany(Slot::class, 'agent_slot')
                    ->withTimestamps()
                    ->withPivot('company_id');
    }
}

