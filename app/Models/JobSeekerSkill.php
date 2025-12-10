<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class JobSeekerSkill extends Pivot
{
    protected $table = 'job_seeker_skill';

    public $timestamps = true;

    public $incrementing = false;

    protected $fillable = [
        'job_seeker_id',
        'skill_id',
    ];
}

