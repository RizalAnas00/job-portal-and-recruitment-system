<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentTransactionFactory> */
    use HasFactory, HasUuids;

    protected $table = 'payment_transactions';

    protected $fillable = [
        'id_company_subscription',
        'amount',
        'payment_date',
        'payment_method',
        'va_number',
        'payment_url',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function companySubscription()
    {
        return $this->belongsTo(CompanySubscription::class, 'id_company_subscription');
    }

    public function isSuccessful()
    {
        return $this->status === 'success';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status === 'pending';
    }
}
