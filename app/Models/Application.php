<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'applications';

	protected $fillable = [
		'id_job_seeker',
		'id_job_posting',
		'application_date',
		'status',
		'cover_letter',
	];

	public function jobSeeker()
	{
		return $this->belongsTo(JobSeeker::class, 'id_job_seeker');
	}

	public function jobPosting()
	{
		return $this->belongsTo(JobPosting::class, 'id_job_posting');
	}
}
