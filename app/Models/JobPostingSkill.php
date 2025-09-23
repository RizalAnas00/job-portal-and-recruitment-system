<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPostingSkill extends Pivot
{
	use SoftDeletes;

	protected $table = 'job_posting_skill';

	public $timestamps = true;

	protected $fillable = [
		'id_job_posting',
		'id_skill',
	];
}
