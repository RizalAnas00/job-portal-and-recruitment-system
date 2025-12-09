<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
        'min_salary',
        'max_salary',
        'posted_date',
        'closing_date',
        'status',
        'moderation_status',
        'rejection_reason',
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
     * Determine the appropriate status based on the schedule window.
     */
    public static function statusForSchedule(?Carbon $openAt, ?Carbon $closeAt): string
    {
        if (!$openAt || !$closeAt) {
            return 'draft';
        }

        $now = now();
        return $now->between($openAt, $closeAt) ? 'open' : 'closed';
    }

    public function scopeActiveAndApproved($query)
    {
        return $query->where('status', 'open')
                     ->where('moderation_status', 'approved')
                     ->whereDate('closing_date', '>=', now());
    }

    /**
     * Sync statuses with their scheduled window (open between, closed outside).
     */
    public static function refreshScheduledStatuses(): void
    {
        $now = now();

        static::query()
            ->whereNotNull('posted_date')
            ->whereNotNull('closing_date')
            ->whereIn('status', ['open', 'closed'])
            ->where('moderation_status', 'approved')
            ->whereColumn('posted_date', '<=', 'closing_date')
            ->where('posted_date', '<=', $now)
            ->where('closing_date', '>=', $now)
            ->where('status', '!=', 'open')
            ->update(['status' => 'open']);

        static::query()
            ->whereNotNull('posted_date')
            ->whereNotNull('closing_date')
            ->whereIn('status', ['open', 'closed'])
            ->where(function ($query) use ($now) {
                $query->where('closing_date', '<', $now)
                    ->orWhere('posted_date', '>', $now);
            })
            ->where('status', '!=', 'closed')
            ->update(['status' => 'closed']);
    }

    public function hasApplied(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var \App\Models\User */
        $user = Auth::user();
        return $this->applications()
            ->where('id_job_seeker', $user->jobSeeker->id)
            ->exists();
    }

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

    /**
     * Get the location of the job posting.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
