<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans'; 

    protected $fillable = [
        'plan_name',            // Sesuaikan dengan DB
        'price',
        'duration_days',
        'job_post_limit',       // Wajib ada
        'allow_verified_badge', // Wajib ada
    ];
}