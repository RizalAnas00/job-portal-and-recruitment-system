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

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::deleted(function ($subscription) {
    //         $subscription->company->update(['is_verified' => false]);
    //     });
    // }

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'id_plan');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'id_company_subscription');
    }
    public function latestPaymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class, 'id_company_subscription')->latestOfMany('payment_date');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }
}