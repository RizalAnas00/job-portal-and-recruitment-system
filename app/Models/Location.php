<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'province',
        'city',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the job postings in this location.
     */
    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'location_id');
    }

    /**
     * Scope a query to only include active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full location name (city, province).
     */
    public function getFullNameAttribute(): string
    {
        if ($this->province && $this->city) {
            return "{$this->city}, {$this->province}";
        }
        return $this->name;
    }
}
