<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_application',
        'interviewer_name',
        'interview_date',
        'interview_type',
        'location',
        'notes',
    ];

    protected $casts = [
        'interview_date' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'id_application');
    }
}