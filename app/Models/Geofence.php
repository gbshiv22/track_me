<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Geofence extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'radius',
        'type',
        'polygon_coordinates',
        'alert_type',
        'is_active',
        'notification_settings',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'polygon_coordinates' => 'array',
        'notification_settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * User who owns the geofence
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a point is inside the geofence
     */
    public function containsPoint(float $latitude, float $longitude): bool
    {
        if ($this->type === 'circular') {
            return $this->isPointInCircle($latitude, $longitude);
        } elseif ($this->type === 'polygon') {
            return $this->isPointInPolygon($latitude, $longitude);
        }
        
        return false;
    }

    /**
     * Check if point is inside circular geofence
     */
    private function isPointInCircle(float $latitude, float $longitude): bool
    {
        $distance = $this->calculateDistance(
            $this->latitude, 
            $this->longitude, 
            $latitude, 
            $longitude
        );
        
        return $distance <= $this->radius;
    }

    /**
     * Check if point is inside polygon geofence
     */
    private function isPointInPolygon(float $latitude, float $longitude): bool
    {
        if (!$this->polygon_coordinates) {
            return false;
        }

        $polygon = $this->polygon_coordinates;
        $inside = false;
        
        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            if ((($polygon[$i]['lat'] > $latitude) !== ($polygon[$j]['lat'] > $latitude)) &&
                ($longitude < ($polygon[$j]['lng'] - $polygon[$i]['lng']) * 
                 ($latitude - $polygon[$i]['lat']) / ($polygon[$j]['lat'] - $polygon[$i]['lat']) + 
                 $polygon[$i]['lng'])) {
                $inside = !$inside;
            }
        }
        
        return $inside;
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

    /**
     * Scope for active geofences
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}