<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
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

    public function activeSubscription()
    {
        return $this->hasOne(CompanySubscription::class, 'id_company')
                    ->where('status', '=' ,'active')
                    ->where('end_date', '>', now());
    }
    
    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class, 'id_company');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
