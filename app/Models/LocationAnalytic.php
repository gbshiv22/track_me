<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationAnalytic extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'location_name',
        'location_type',
        'visit_count',
        'total_time_spent',
        'first_visited_at',
        'last_visited_at',
        'visit_patterns',
        'time_distribution',
        'average_duration',
        'is_significant',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'first_visited_at' => 'datetime',
        'last_visited_at' => 'datetime',
        'visit_patterns' => 'array',
        'time_distribution' => 'array',
        'average_duration' => 'decimal:2',
        'is_significant' => 'boolean',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update visit statistics
     */
    public function updateVisit(int $timeSpent): void
    {
        $this->visit_count++;
        $this->total_time_spent += $timeSpent;
        $this->average_duration = $this->total_time_spent / $this->visit_count;
        $this->last_visited_at = now();
        
        // Mark as significant if visited frequently
        $this->is_significant = $this->visit_count >= 5;
        
        $this->save();
    }

    /**
     * Get formatted total time spent
     */
    public function getFormattedTimeSpentAttribute(): string
    {
        $hours = floor($this->total_time_spent / 3600);
        $minutes = floor(($this->total_time_spent % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%d hours %d minutes', $hours, $minutes);
        }
        
        return sprintf('%d minutes', $minutes);
    }

    /**
     * Get visit frequency (visits per week)
     */
    public function getVisitFrequencyAttribute(): float
    {
        if (!$this->first_visited_at) {
            return 0;
        }

        $daysSinceFirst = $this->first_visited_at->diffInDays(now());
        if ($daysSinceFirst === 0) {
            return $this->visit_count;
        }

        return round(($this->visit_count / $daysSinceFirst) * 7, 2);
    }

    /**
     * Get heat map intensity (0-100)
     */
    public function getHeatIntensityAttribute(): int
    {
        $maxVisits = 100; // Maximum visits for normalization
        $intensity = min(100, ($this->visit_count / $maxVisits) * 100);
        
        // Boost intensity for recent visits
        $daysSinceLastVisit = $this->last_visited_at->diffInDays(now());
        if ($daysSinceLastVisit <= 7) {
            $intensity = min(100, $intensity * 1.2);
        }
        
        return (int) $intensity;
    }

    /**
     * Scope for significant locations
     */
    public function scopeSignificant($query)
    {
        return $query->where('is_significant', true);
    }

    /**
     * Scope for recent locations
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('last_visited_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for location type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('location_type', $type);
    }
}