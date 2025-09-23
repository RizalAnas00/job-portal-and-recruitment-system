<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'job_postings';

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

	public function company()
	{
		return $this->belongsTo(Company::class, 'id_company');
	}

	public function skills()
	{
		return $this->belongsToMany(Skill::class, 'job_posting_skill', 'id_job_posting', 'id_skill')
			->using(JobPostingSkill::class)
			->withTimestamps();
	}

	public function applications()
	{
		return $this->hasMany(Application::class, 'id_job_posting');
	}
}
