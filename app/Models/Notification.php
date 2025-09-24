<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'id_job_seeker',
        'id_company',
        'message',
        'is_read',
        'link_url',
    ];

    public function jobSeeker()
    {
        return $this->belongsTo(JobSeeker::class, 'id_job_seeker');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }
}
