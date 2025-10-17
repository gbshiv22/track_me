<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationPoint extends Model
{
    protected $fillable = [
        'tracking_session_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'altitude',
        'battery_level',
        'is_offline',
        'synced_at',
        'metadata',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'synced_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'altitude' => 'decimal:2',
        'battery_level' => 'integer',
        'is_offline' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Tracking session relationship
     */
    public function trackingSession()
    {
        return $this->belongsTo(TrackingSession::class);
    }
}
