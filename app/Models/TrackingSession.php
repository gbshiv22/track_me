<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingSession extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'started_at',
        'stopped_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
        'start_latitude' => 'decimal:7',
        'start_longitude' => 'decimal:7',
        'end_latitude' => 'decimal:7',
        'end_longitude' => 'decimal:7',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Location points relationship
     */
    public function locationPoints()
    {
        return $this->hasMany(LocationPoint::class);
    }

    /**
     * Get the latest location point
     */
    public function latestLocation()
    {
        return $this->hasOne(LocationPoint::class)->latestOfMany('recorded_at');
    }

    /**
     * Trip statistics relationship
     */
    public function tripStatistic()
    {
        return $this->hasOne(TripStatistic::class);
    }

    /**
     * Location shares for this session
     */
    public function locationShares()
    {
        return $this->hasMany(LocationShare::class);
    }

    /**
     * Calculate total distance of the trip
     */
    public function calculateTotalDistance(): float
    {
        $points = $this->locationPoints()->orderBy('recorded_at')->get();
        $totalDistance = 0;

        for ($i = 1; $i < count($points); $i++) {
            $distance = $this->calculateDistance(
                $points[$i-1]->latitude,
                $points[$i-1]->longitude,
                $points[$i]->latitude,
                $points[$i]->longitude
            );
            $totalDistance += $distance;
        }

        return $totalDistance / 1000; // Convert to kilometers
    }

    /**
     * Calculate trip duration in seconds
     */
    public function calculateDuration(): int
    {
        if (!$this->started_at || !$this->stopped_at) {
            return 0;
        }

        return $this->started_at->diffInSeconds($this->stopped_at);
    }

    /**
     * Get average speed in km/h
     */
    public function getAverageSpeed(): float
    {
        $distance = $this->calculateTotalDistance();
        $duration = $this->calculateDuration();

        if ($duration === 0) {
            return 0;
        }

        return ($distance / $duration) * 3600; // Convert to km/h
    }

    /**
     * Calculate distance between two points in meters
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}
