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
		'id_resume',
		'status',
		'cover_letter',
	];

	protected $casts = [
		'application_date' => 'datetime',
	];
	
	protected static function booted()
	{
		static::addGlobalScope('active', function ($query) {
			$query->whereNull('deleted_at');
		});	
	}

	/**
	 * Update status lamaran
	 */
	public function updateStatus($status)
	{
		$this->status = $status;
		$this->save();
	}

	public function jobSeeker()
	{
		return $this->belongsTo(JobSeeker::class, 'id_job_seeker');
	}

	public function jobPosting()
	{
		return $this->belongsTo(JobPosting::class, 'id_job_posting');
	}

	public function resume()
	{
		return $this->belongsTo(Resume::class, 'id_resume');
	}
}
