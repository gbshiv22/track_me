<?php

namespace App\Services;

use App\Models\TripStatistic;
use App\Models\TrackingSession;
use App\Models\LocationPoint;
use Illuminate\Support\Facades\DB;

class TripAnalyticsService
{
    /**
     * Generate trip statistics for a tracking session
     */
    public function generateTripStatistics(TrackingSession $session): TripStatistic
    {
        $points = $session->locationPoints()->orderBy('recorded_at')->get();
        
        if ($points->isEmpty()) {
            throw new \Exception('No location points found for this session');
        }

        $totalDistance = $this->calculateTotalDistance($points);
        $totalDuration = $session->calculateDuration();
        $speedData = $this->analyzeSpeedData($points);
        $transportMode = $this->detectTransportMode($speedData);
        $carbonFootprint = $this->calculateCarbonFootprint($totalDistance, $transportMode);
        $batteryUsage = $this->calculateBatteryUsage($points);
        $routeEfficiency = $this->analyzeRouteEfficiency($points);

        return TripStatistic::create([
            'tracking_session_id' => $session->id,
            'user_id' => $session->user_id,
            'total_distance' => $totalDistance,
            'total_duration' => $totalDuration,
            'average_speed' => $speedData['average'],
            'max_speed' => $speedData['max'],
            'min_speed' => $speedData['min'],
            'total_points' => $points->count(),
            'carbon_footprint' => $carbonFootprint,
            'transport_mode' => $transportMode,
            'speed_analysis' => $speedData,
            'route_efficiency' => $routeEfficiency,
            'battery_usage' => $batteryUsage,
        ]);
    }

    /**
     * Calculate total distance from location points
     */
    private function calculateTotalDistance($points): float
    {
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
     * Analyze speed data from location points
     */
    private function analyzeSpeedData($points): array
    {
        $speeds = $points->whereNotNull('speed')->pluck('speed')->toArray();
        
        if (empty($speeds)) {
            return [
                'average' => 0,
                'max' => 0,
                'min' => 0,
                'distribution' => [],
            ];
        }

        return [
            'average' => round(array_sum($speeds) / count($speeds), 2),
            'max' => max($speeds),
            'min' => min($speeds),
            'distribution' => $this->getSpeedDistribution($speeds),
        ];
    }

    /**
     * Get speed distribution
     */
    private function getSpeedDistribution(array $speeds): array
    {
        $ranges = [
            '0-10' => 0,
            '10-20' => 0,
            '20-30' => 0,
            '30-40' => 0,
            '40-50' => 0,
            '50+' => 0,
        ];

        foreach ($speeds as $speed) {
            if ($speed <= 10) {
                $ranges['0-10']++;
            } elseif ($speed <= 20) {
                $ranges['10-20']++;
            } elseif ($speed <= 30) {
                $ranges['20-30']++;
            } elseif ($speed <= 40) {
                $ranges['30-40']++;
            } elseif ($speed <= 50) {
                $ranges['40-50']++;
            } else {
                $ranges['50+']++;
            }
        }

        return $ranges;
    }

    /**
     * Detect transport mode based on speed patterns
     */
    private function detectTransportMode(array $speedData): string
    {
        $averageSpeed = $speedData['average'];
        $maxSpeed = $speedData['max'];

        if ($averageSpeed < 5) {
            return 'walking';
        } elseif ($averageSpeed < 15) {
            return 'cycling';
        } elseif ($averageSpeed < 30) {
            return 'public_transport';
        } elseif ($maxSpeed > 80) {
            return 'driving';
        } else {
            return 'unknown';
        }
    }

    /**
     * Calculate carbon footprint
     */
    private function calculateCarbonFootprint(float $distance, string $transportMode): float
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

        $factor = $emissionFactors[$transportMode] ?? 0.192;
        return round($distance * $factor, 2);
    }

    /**
     * Calculate battery usage
     */
    private function calculateBatteryUsage($points): ?float
    {
        $batteryLevels = $points->whereNotNull('battery_level')->pluck('battery_level')->toArray();
        
        if (count($batteryLevels) < 2) {
            return null;
        }

        $startBattery = $batteryLevels[0];
        $endBattery = end($batteryLevels);
        
        return $startBattery - $endBattery;
    }

    /**
     * Analyze route efficiency
     */
    private function analyzeRouteEfficiency($points): array
    {
        if (count($points) < 2) {
            return ['directness' => 0, 'speed_consistency' => 0];
        }

        $totalDistance = $this->calculateTotalDistance($points);
        $directDistance = $this->calculateDistance(
            $points->first()->latitude,
            $points->first()->longitude,
            $points->last()->latitude,
            $points->last()->longitude
        ) / 1000;

        $directness = $directDistance > 0 ? min(1, $directDistance / $totalDistance) : 0;
        
        $speeds = $points->whereNotNull('speed')->pluck('speed')->toArray();
        $speedConsistency = $this->calculateSpeedConsistency($speeds);

        return [
            'directness' => round($directness, 2),
            'speed_consistency' => round($speedConsistency, 2),
        ];
    }

    /**
     * Calculate speed consistency (0-1, higher is more consistent)
     */
    private function calculateSpeedConsistency(array $speeds): float
    {
        if (count($speeds) < 2) {
            return 0;
        }

        $average = array_sum($speeds) / count($speeds);
        $variance = array_sum(array_map(function($speed) use ($average) {
            return pow($speed - $average, 2);
        }, $speeds)) / count($speeds);

        $standardDeviation = sqrt($variance);
        
        // Convert to consistency score (0-1)
        return max(0, 1 - ($standardDeviation / $average));
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
     * Get user's trip statistics summary
     */
    public function getUserTripSummary($userId, int $days = 30): array
    {
        $stats = TripStatistic::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        if ($stats->isEmpty()) {
            return [
                'total_trips' => 0,
                'total_distance' => 0,
                'total_duration' => 0,
                'average_speed' => 0,
                'total_carbon_footprint' => 0,
                'transport_modes' => [],
            ];
        }

        return [
            'total_trips' => $stats->count(),
            'total_distance' => round($stats->sum('total_distance'), 2),
            'total_duration' => $stats->sum('total_duration'),
            'average_speed' => round($stats->avg('average_speed'), 2),
            'total_carbon_footprint' => round($stats->sum('carbon_footprint'), 2),
            'transport_modes' => $stats->groupBy('transport_mode')->map->count(),
        ];
    }
}
