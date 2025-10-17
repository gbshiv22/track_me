<?php

namespace App\Services;

use App\Models\LocationAnalytic;
use App\Models\User;
use App\Models\LocationPoint;
use Illuminate\Support\Facades\DB;

class LocationAnalyticsService
{
    /**
     * Analyze location data and update analytics
     */
    public function analyzeLocationData(User $user, array $locationPoints): void
    {
        $groupedPoints = $this->groupLocationPoints($locationPoints);
        
        foreach ($groupedPoints as $location => $points) {
            $this->updateLocationAnalytics($user, $location, $points);
        }
    }

    /**
     * Group location points by proximity
     */
    private function groupLocationPoints(array $points): array
    {
        $groups = [];
        $threshold = 100; // 100 meters threshold for grouping

        foreach ($points as $point) {
            $grouped = false;
            
            foreach ($groups as $key => $group) {
                $representative = $group[0];
                $distance = $this->calculateDistance(
                    $point['latitude'],
                    $point['longitude'],
                    $representative['latitude'],
                    $representative['longitude']
                );

                if ($distance <= $threshold) {
                    $groups[$key][] = $point;
                    $grouped = true;
                    break;
                }
            }

            if (!$grouped) {
                $groups[] = [$point];
            }
        }

        return $groups;
    }

    /**
     * Update location analytics for a specific location
     */
    private function updateLocationAnalytics(User $user, array $points, array $locationData): void
    {
        $representative = $points[0];
        $latitude = round($representative['latitude'], 4);
        $longitude = round($representative['longitude'], 4);

        $analytic = LocationAnalytic::where('user_id', $user->id)
            ->where('latitude', $latitude)
            ->where('longitude', $longitude)
            ->first();

        if (!$analytic) {
            $analytic = LocationAnalytic::create([
                'user_id' => $user->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_name' => $this->reverseGeocode($latitude, $longitude),
                'location_type' => $this->detectLocationType($points),
                'visit_count' => 0,
                'total_time_spent' => 0,
                'first_visited_at' => now(),
                'last_visited_at' => now(),
                'average_duration' => 0,
                'is_significant' => false,
            ]);
        }

        $timeSpent = $this->calculateTimeSpent($points);
        $analytic->updateVisit($timeSpent);
        
        $this->updateVisitPatterns($analytic, $points);
        $this->updateTimeDistribution($analytic, $points);
    }

    /**
     * Calculate time spent at location
     */
    private function calculateTimeSpent(array $points): int
    {
        if (count($points) < 2) {
            return 0;
        }

        $firstPoint = $points[0];
        $lastPoint = end($points);
        
        return strtotime($lastPoint['recorded_at']) - strtotime($firstPoint['recorded_at']);
    }

    /**
     * Detect location type based on patterns
     */
    private function detectLocationType(array $points): string
    {
        $timeSpent = $this->calculateTimeSpent($points);
        $pointCount = count($points);

        if ($timeSpent > 3600) { // More than 1 hour
            return 'home';
        } elseif ($timeSpent > 1800) { // More than 30 minutes
            return 'work';
        } elseif ($timeSpent > 300) { // More than 5 minutes
            return 'restaurant';
        } else {
            return 'transit';
        }
    }

    /**
     * Update visit patterns
     */
    private function updateVisitPatterns(LocationAnalytic $analytic, array $points): void
    {
        $patterns = $analytic->visit_patterns ?? [];
        
        foreach ($points as $point) {
            $dayOfWeek = date('w', strtotime($point['recorded_at']));
            $hour = date('H', strtotime($point['recorded_at']));
            
            if (!isset($patterns[$dayOfWeek])) {
                $patterns[$dayOfWeek] = [];
            }
            
            if (!isset($patterns[$dayOfWeek][$hour])) {
                $patterns[$dayOfWeek][$hour] = 0;
            }
            
            $patterns[$dayOfWeek][$hour]++;
        }

        $analytic->update(['visit_patterns' => $patterns]);
    }

    /**
     * Update time distribution
     */
    private function updateTimeDistribution(LocationAnalytic $analytic, array $points): void
    {
        $distribution = $analytic->time_distribution ?? [];
        
        foreach ($points as $point) {
            $hour = date('H', strtotime($point['recorded_at']));
            
            if (!isset($distribution[$hour])) {
                $distribution[$hour] = 0;
            }
            
            $distribution[$hour]++;
        }

        $analytic->update(['time_distribution' => $distribution]);
    }

    /**
     * Get user's location analytics summary
     */
    public function getUserLocationSummary(User $user, int $days = 30): array
    {
        $analytics = LocationAnalytic::where('user_id', $user->id)
            ->where('last_visited_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total_locations' => $analytics->count(),
            'significant_locations' => $analytics->where('is_significant', true)->count(),
            'total_visits' => $analytics->sum('visit_count'),
            'total_time_spent' => $analytics->sum('total_time_spent'),
            'most_visited_location' => $analytics->sortByDesc('visit_count')->first(),
            'location_types' => $analytics->groupBy('location_type')->map->count(),
        ];
    }

    /**
     * Get heat map data for user
     */
    public function getHeatMapData(User $user, int $days = 30): array
    {
        return LocationAnalytic::where('user_id', $user->id)
            ->where('last_visited_at', '>=', now()->subDays($days))
            ->get()
            ->map(function ($analytic) {
                return [
                    'latitude' => $analytic->latitude,
                    'longitude' => $analytic->longitude,
                    'intensity' => $analytic->heat_intensity,
                    'visit_count' => $analytic->visit_count,
                    'location_name' => $analytic->location_name,
                    'location_type' => $analytic->location_type,
                ];
            })
            ->toArray();
    }

    /**
     * Get frequently visited locations
     */
    public function getFrequentLocations(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return LocationAnalytic::where('user_id', $user->id)
            ->where('is_significant', true)
            ->orderBy('visit_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get location insights
     */
    public function getLocationInsights(User $user): array
    {
        $analytics = LocationAnalytic::where('user_id', $user->id)->get();
        
        if ($analytics->isEmpty()) {
            return [];
        }

        $insights = [];
        
        // Most visited location
        $mostVisited = $analytics->sortByDesc('visit_count')->first();
        if ($mostVisited) {
            $insights[] = [
                'type' => 'most_visited',
                'message' => "You've visited {$mostVisited->location_name} {$mostVisited->visit_count} times",
                'data' => $mostVisited,
            ];
        }

        // Recent activity
        $recentLocations = $analytics->where('last_visited_at', '>=', now()->subDays(7));
        if ($recentLocations->count() > 0) {
            $insights[] = [
                'type' => 'recent_activity',
                'message' => "You've been to {$recentLocations->count()} different locations this week",
                'data' => $recentLocations->count(),
            ];
        }

        // Time patterns
        $totalTime = $analytics->sum('total_time_spent');
        $averageTime = $totalTime / $analytics->count();
        $insights[] = [
            'type' => 'time_patterns',
            'message' => "You spend an average of " . round($averageTime / 60, 1) . " minutes per location",
            'data' => round($averageTime / 60, 1),
        ];

        return $insights;
    }

    /**
     * Reverse geocode coordinates to get location name
     */
    private function reverseGeocode(float $latitude, float $longitude): string
    {
        // In a real implementation, you'd use a geocoding service like Google Maps API
        // For now, return a placeholder
        return "Location at {$latitude}, {$longitude}";
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
