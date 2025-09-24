<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionPlanFactory> */
    use HasFactory;
    
    protected $fillable = [
        'plan_name',
        'price',
        'duration_days',
        'job_post_limit',
        'allow_verified_badge',
    ];
}
