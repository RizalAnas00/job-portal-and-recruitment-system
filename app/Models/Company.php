<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone_number',
        'website',
        'company_description',
        'industry',
        'address',
        'is_verified',
    ];

    protected $hidden = [
        'password',
    ];

    // public function jobPostings()
    // {
    //     return $this->hasMany(JobPosting::class);
    // }

    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class);
    // }

    // public function companySubscription()
    // {
    //     return $this->hasOne(CompanySubscription::class);
    // }
}
