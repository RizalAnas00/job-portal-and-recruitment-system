<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone_number',
        'company_name',
        'company_description',
        'website',
        'industry',
        'address',
        'is_verified',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the company.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job postings for the company.
     */
    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'id_company');
    }

    /**
     * Get the currently active subscription for the company.
     */
    public function activeSubscription(): HasOne
    {
        // Menentukan 'id_company' sebagai foreign key sangat penting agar relasi ini berfungsi.
        return $this->hasOne(CompanySubscription::class, 'id_company')
                    ->where('status', 'active')
                    ->where('end_date', '>', now());
    }
}
