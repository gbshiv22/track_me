<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TripAnalyticsService;
use App\Services\LocationAnalyticsService;
use App\Models\TripStatistic;
use App\Models\LocationAnalytic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    protected $tripAnalyticsService;
    protected $locationAnalyticsService;

    public function __construct(
        TripAnalyticsService $tripAnalyticsService,
        LocationAnalyticsService $locationAnalyticsService
    ) {
        $this->tripAnalyticsService = $tripAnalyticsService;
        $this->locationAnalyticsService = $locationAnalyticsService;
    }

    /**
     * Get trip statistics summary
     */
    public function getTripSummary(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $summary = $this->tripAnalyticsService->getUserTripSummary(auth()->id(), $days);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get detailed trip statistics
     */
    public function getTripStatistics(Request $request): JsonResponse
    {
        $query = TripStatistic::where('user_id', auth()->id())
            ->with('trackingSession');

        if ($request->has('days')) {
            $query->where('created_at', '>=', now()->subDays($request->get('days')));
        }

        if ($request->has('transport_mode')) {
            $query->where('transport_mode', $request->get('transport_mode'));
        }

        $statistics = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get location analytics summary
     */
    public function getLocationSummary(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $summary = $this->locationAnalyticsService->getUserLocationSummary(auth()->user(), $days);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get heat map data
     */
    public function getHeatMapData(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $heatMapData = $this->locationAnalyticsService->getHeatMapData(auth()->user(), $days);

        return response()->json([
            'success' => true,
            'data' => $heatMapData,
        ]);
    }

    /**
     * Get frequently visited locations
     */
    public function getFrequentLocations(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $locations = $this->locationAnalyticsService->getFrequentLocations(auth()->user(), $limit);

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    /**
     * Get location insights
     */
    public function getLocationInsights(): JsonResponse
    {
        $insights = $this->locationAnalyticsService->getLocationInsights(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $insights,
        ]);
    }

    /**
     * Get weekly report
     */
    public function getWeeklyReport(): JsonResponse
    {
        $tripSummary = $this->tripAnalyticsService->getUserTripSummary(auth()->id(), 7);
        $locationSummary = $this->locationAnalyticsService->getUserLocationSummary(auth()->user(), 7);
        $insights = $this->locationAnalyticsService->getLocationInsights(auth()->user());

        return response()->json([
            'success' => true,
            'data' => [
                'trip_summary' => $tripSummary,
                'location_summary' => $locationSummary,
                'insights' => $insights,
                'period' => 'weekly',
                'generated_at' => now(),
            ],
        ]);
    }

    /**
     * Get monthly report
     */
    public function getMonthlyReport(): JsonResponse
    {
        $tripSummary = $this->tripAnalyticsService->getUserTripSummary(auth()->id(), 30);
        $locationSummary = $this->locationAnalyticsService->getUserLocationSummary(auth()->user(), 30);
        $insights = $this->locationAnalyticsService->getLocationInsights(auth()->user());

        return response()->json([
            'success' => true,
            'data' => [
                'trip_summary' => $tripSummary,
                'location_summary' => $locationSummary,
                'insights' => $insights,
                'period' => 'monthly',
                'generated_at' => now(),
            ],
        ]);
    }

    /**
     * Get carbon footprint summary
     */
    public function getCarbonFootprintSummary(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $carbonData = TripStatistic::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('
                SUM(carbon_footprint) as total_carbon,
                AVG(carbon_footprint) as avg_carbon_per_trip,
                transport_mode,
                COUNT(*) as trip_count
            ')
            ->groupBy('transport_mode')
            ->get();

        $totalCarbon = $carbonData->sum('total_carbon');
        $totalTrips = $carbonData->sum('trip_count');

        return response()->json([
            'success' => true,
            'data' => [
                'total_carbon_footprint' => round($totalCarbon, 2),
                'average_carbon_per_trip' => $totalTrips > 0 ? round($totalCarbon / $totalTrips, 2) : 0,
                'by_transport_mode' => $carbonData,
                'period_days' => $days,
            ],
        ]);
    }

    /**
     * Get speed analysis
     */
    public function getSpeedAnalysis(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $speedData = TripStatistic::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('
                AVG(average_speed) as avg_speed,
                MAX(max_speed) as max_speed,
                MIN(min_speed) as min_speed,
                transport_mode,
                COUNT(*) as trip_count
            ')
            ->groupBy('transport_mode')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'speed_analysis' => $speedData,
                'period_days' => $days,
            ],
        ]);
    }

    /**
     * Get travel calendar data
     */
    public function getTravelCalendar(Request $request): JsonResponse
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $calendarData = TripStatistic::where('user_id', auth()->id())
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('trackingSession')
            ->get()
            ->groupBy(function($trip) {
                return $trip->created_at->format('Y-m-d');
            })
            ->map(function($trips) {
                return [
                    'date' => $trips->first()->created_at->format('Y-m-d'),
                    'trip_count' => $trips->count(),
                    'total_distance' => round($trips->sum('total_distance'), 2),
                    'total_duration' => $trips->sum('total_duration'),
                    'carbon_footprint' => round($trips->sum('carbon_footprint'), 2),
                    'transport_modes' => $trips->pluck('transport_mode')->unique()->values(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'calendar_data' => $calendarData,
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }
}