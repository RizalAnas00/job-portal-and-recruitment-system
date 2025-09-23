<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSeeker extends Model
{
    /** @use HasFactory<\Database\Factories\JobSeekerFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'address',
        'profile_summary',
    ];

    protected $hidden = [
        'password',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class, 'id_job_seeker');
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }

    // public function skills()
    // {
    //     return $this->belongsToMany(Skill::class, 'job_seeker_skills');
    // }

    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class);
    // }
}
