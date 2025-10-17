<?php

namespace App\Services;

use App\Models\LocationPoint;
use App\Models\TrackingSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OfflineSyncService
{
    /**
     * Store location point for offline sync
     */
    public function storeOfflineLocationPoint(array $locationData, int $userId): LocationPoint
    {
        return LocationPoint::create([
            'tracking_session_id' => $locationData['tracking_session_id'],
            'latitude' => $locationData['latitude'],
            'longitude' => $locationData['longitude'],
            'accuracy' => $locationData['accuracy'] ?? null,
            'speed' => $locationData['speed'] ?? null,
            'heading' => $locationData['heading'] ?? null,
            'altitude' => $locationData['altitude'] ?? null,
            'battery_level' => $locationData['battery_level'] ?? null,
            'is_offline' => true,
            'recorded_at' => $locationData['recorded_at'] ?? now(),
            'metadata' => $locationData['metadata'] ?? null,
        ]);
    }

    /**
     * Sync offline location points when connection is restored
     */
    public function syncOfflinePoints(int $userId): array
    {
        $offlinePoints = LocationPoint::whereHas('trackingSession', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('is_offline', true)
        ->whereNull('synced_at')
        ->get();

        $syncedCount = 0;
        $errors = [];

        foreach ($offlinePoints as $point) {
            try {
                $this->syncLocationPoint($point);
                $syncedCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'point_id' => $point->id,
                    'error' => $e->getMessage(),
                ];
                Log::error('Failed to sync offline point', [
                    'point_id' => $point->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'synced_count' => $syncedCount,
            'total_points' => $offlinePoints->count(),
            'errors' => $errors,
        ];
    }

    /**
     * Sync individual location point
     */
    private function syncLocationPoint(LocationPoint $point): void
    {
        // Mark as synced
        $point->update([
            'is_offline' => false,
            'synced_at' => now(),
        ]);

        // Process the point for analytics
        $this->processSyncedPoint($point);
    }

    /**
     * Process synced point for analytics
     */
    private function processSyncedPoint(LocationPoint $point): void
    {
        // Check geofence alerts
        $geofencingService = app(GeofencingService::class);
        $alerts = $geofencingService->checkGeofenceAlerts($point);
        
        if (!empty($alerts)) {
            Log::info('Geofence alerts triggered for synced point', [
                'point_id' => $point->id,
                'alerts' => $alerts,
            ]);
        }

        // Update location analytics
        $analyticsService = app(LocationAnalyticsService::class);
        $analyticsService->analyzeLocationData($point->trackingSession->user, [
            [
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'recorded_at' => $point->recorded_at->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Get offline sync status for user
     */
    public function getOfflineSyncStatus(int $userId): array
    {
        $offlineCount = LocationPoint::whereHas('trackingSession', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('is_offline', true)
        ->whereNull('synced_at')
        ->count();

        $lastSyncTime = LocationPoint::whereHas('trackingSession', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereNotNull('synced_at')
        ->latest('synced_at')
        ->value('synced_at');

        return [
            'offline_points_count' => $offlineCount,
            'last_sync_time' => $lastSyncTime,
            'needs_sync' => $offlineCount > 0,
        ];
    }

    /**
     * Optimize battery usage for offline tracking
     */
    public function optimizeBatteryUsage(int $userId): array
    {
        $recommendations = [];

        // Check current battery level
        $latestPoint = LocationPoint::whereHas('trackingSession', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereNotNull('battery_level')
        ->latest('recorded_at')
        ->first();

        if ($latestPoint && $latestPoint->battery_level < 20) {
            $recommendations[] = [
                'type' => 'battery_low',
                'message' => 'Battery is low. Consider reducing tracking frequency.',
                'action' => 'reduce_frequency',
            ];
        }

        // Check tracking frequency
        $recentPoints = LocationPoint::whereHas('trackingSession', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('recorded_at', '>=', now()->subMinutes(10))
        ->count();

        if ($recentPoints > 20) {
            $recommendations[] = [
                'type' => 'high_frequency',
                'message' => 'High tracking frequency detected. Consider reducing to save battery.',
                'action' => 'reduce_frequency',
            ];
        }

        // Check offline points accumulation
        $offlineCount = $this->getOfflineSyncStatus($userId)['offline_points_count'];
        if ($offlineCount > 100) {
            $recommendations[] = [
                'type' => 'sync_needed',
                'message' => 'Many offline points need syncing. Connect to internet when possible.',
                'action' => 'sync_when_connected',
            ];
        }

        return $recommendations;
    }

    /**
     * Get offline tracking statistics
     */
    public function getOfflineStats(int $userId, int $days = 30): array
    {
        $stats = LocationPoint::whereHas('trackingSession', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('recorded_at', '>=', now()->subDays($days))
        ->selectRaw('
            COUNT(*) as total_points,
            SUM(CASE WHEN is_offline = 1 THEN 1 ELSE 0 END) as offline_points,
            SUM(CASE WHEN synced_at IS NOT NULL THEN 1 ELSE 0 END) as synced_points,
            AVG(battery_level) as avg_battery_level
        ')
        ->first();

        return [
            'total_points' => $stats->total_points ?? 0,
            'offline_points' => $stats->offline_points ?? 0,
            'synced_points' => $stats->synced_points ?? 0,
            'sync_rate' => $stats->total_points > 0 ? 
                round(($stats->synced_points / $stats->total_points) * 100, 2) : 0,
            'avg_battery_level' => round($stats->avg_battery_level ?? 0, 2),
        ];
    }

    /**
     * Clean up old synced offline points
     */
    public function cleanupOldSyncedPoints(int $days = 30): int
    {
        return LocationPoint::where('is_offline', false)
            ->whereNotNull('synced_at')
            ->where('synced_at', '<', now()->subDays($days))
            ->delete();
    }
}
