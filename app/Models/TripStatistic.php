<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripStatistic extends Model
{
    protected $fillable = [
        'tracking_session_id',
        'user_id',
        'total_distance',
        'total_duration',
        'average_speed',
        'max_speed',
        'min_speed',
        'total_points',
        'carbon_footprint',
        'transport_mode',
        'speed_analysis',
        'route_efficiency',
        'battery_usage',
    ];

    protected $casts = [
        'total_distance' => 'decimal:2',
        'average_speed' => 'decimal:2',
        'max_speed' => 'decimal:2',
        'min_speed' => 'decimal:2',
        'carbon_footprint' => 'decimal:2',
        'battery_usage' => 'decimal:2',
        'speed_analysis' => 'array',
        'route_efficiency' => 'array',
    ];

    /**
     * Tracking session relationship
     */
    public function trackingSession(): BelongsTo
    {
        return $this->belongsTo(TrackingSession::class);
    }

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate carbon footprint based on transport mode
     */
    public function calculateCarbonFootprint(): float
    {
        $emissionFactors = [
            'walking' => 0,
            'cycling' => 0,
            'driving' => 0.192, // kg CO2 per km
            'public_transport' => 0.041,
            'motorcycle' => 0.103,
            'bus' => 0.089,
            'train' => 0.041,
        ];

        $factor = $emissionFactors[$this->transport_mode] ?? 0.192;
        return $this->total_distance * $factor;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->total_duration / 3600);
        $minutes = floor(($this->total_duration % 3600) / 60);
        $seconds = $this->total_duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get efficiency score (0-100)
     */
    public function getEfficiencyScoreAttribute(): int
    {
        if (!$this->route_efficiency) {
            return 0;
        }

        $score = 0;
        
        // Speed consistency (30 points)
        if (isset($this->route_efficiency['speed_consistency'])) {
            $score += min(30, $this->route_efficiency['speed_consistency'] * 30);
        }
        
        // Route directness (40 points)
        if (isset($this->route_efficiency['directness'])) {
            $score += min(40, $this->route_efficiency['directness'] * 40);
        }
        
        // Battery efficiency (30 points)
        if ($this->battery_usage && $this->battery_usage < 10) {
            $score += 30;
        } elseif ($this->battery_usage && $this->battery_usage < 20) {
            $score += 20;
        } elseif ($this->battery_usage && $this->battery_usage < 30) {
            $score += 10;
        }

        return min(100, $score);
    }

    /**
     * Scope for recent trips
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}