<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatePlanRule extends Model
{
    protected $fillable = ['rate_plan_id','room_id','base_price','extra_bed_price'.'company_id'];

    public function ratePlan() {
        return $this->belongsTo(RatePlan::class);
    }
}
