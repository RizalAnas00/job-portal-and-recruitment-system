<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPostingSkill extends Pivot
{
	use HasFactory, SoftDeletes;

	protected $table = 'job_posting_skill';

	public $timestamps = true;
	public $incrementing = false;

	protected $fillable = [
		'id_job_posting',
		'id_skill',
	];

	protected $dates = [
		'deleted_at',
	];
}
