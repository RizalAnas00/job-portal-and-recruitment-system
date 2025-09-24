<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_company',
        'id_plan',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'id_plan');
    }
}