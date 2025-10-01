<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'skills';

	protected $fillable = [
		'skill_name',
	];

	public function jobPostings()
	{
		return $this->belongsToMany(JobPosting::class, 'job_posting_skill', 'id_skill', 'id_job_posting')
			->using(JobPostingSkill::class)
			->withTimestamps();
	}

	public function jobSeekers()
	{
		return $this->belongsToMany(JobSeeker::class, 'job_seeker_skills', 'id_skill', 'id_job_seeker')
			->using(JobSeekerSkill::class)
			->withTimestamps();
	}
}
