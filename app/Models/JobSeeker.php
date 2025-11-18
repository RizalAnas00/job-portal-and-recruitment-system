<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSeeker extends Model
{
    /** @use HasFactory<\Database\Factories\JobSeekerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'profile_summary',
    ];

    // protected $hidden = [
    //     'password',
    // ];

    public function applications()
    {
        return $this->hasMany(Application::class, 'id_job_seeker');
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function skills()
    {
         return $this->belongsToMany(Skill::class, 'job_seeker_skill', 'job_seeker_id', 'skill_id')
            ->using(JobSeekerSkill::class)
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_job_seeker');
    }
}
