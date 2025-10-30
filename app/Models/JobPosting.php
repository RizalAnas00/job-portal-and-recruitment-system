<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_company',
        'job_title',
        'job_description',
        'location',
        'job_type',
        'salary_range',
        'posted_date',
        'closing_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'posted_date' => 'datetime',
        'closing_date' => 'datetime',
    ];

    /**
     * Get the company that posted the job.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    /**
     * The skills that are required for the job posting.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_posting_skill', 'id_job_posting', 'id_skill');
    }

    /**
     * Get the applications for the job posting.
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'id_job_posting');
    }
}
