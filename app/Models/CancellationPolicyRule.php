<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancellationPolicyRule extends Model
{
    protected $fillable = ['cancellation_policy_id','days_from','days_to','penalty_percent','refundable','company_id'];
}

