<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TripAnalyticsService;
use App\Services\LocationAnalyticsService;
use Illuminate\Http\Request;

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
     * Show analytics dashboard
     */
    public function index()
    {
        $tripSummary = $this->tripAnalyticsService->getUserTripSummary(auth()->id(), 30);
        $locationSummary = $this->locationAnalyticsService->getUserLocationSummary(auth()->user(), 30);
        $insights = $this->locationAnalyticsService->getLocationInsights(auth()->user());

        return view('analytics.index', compact('tripSummary', 'locationSummary', 'insights'));
    }

    /**
     * Show trip statistics
     */
    public function tripStatistics(Request $request)
    {
        $days = $request->get('days', 30);
        $transportMode = $request->get('transport_mode');
        
        $query = \App\Models\TripStatistic::where('user_id', auth()->id())
            ->with('trackingSession');

        if ($days) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        if ($transportMode) {
            $query->where('transport_mode', $transportMode);
        }

        $statistics = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('analytics.trip-statistics', compact('statistics', 'days', 'transportMode'));
    }

    /**
     * Show heat map
     */
    public function heatMap(Request $request)
    {
        $days = $request->get('days', 30);
        $heatMapData = $this->locationAnalyticsService->getHeatMapData(auth()->user(), $days);

        return view('analytics.heat-map', compact('heatMapData', 'days'));
    }

    /**
     * Show reports
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'weekly');
        $days = $period === 'weekly' ? 7 : 30;

        $tripSummary = $this->tripAnalyticsService->getUserTripSummary(auth()->id(), $days);
        $locationSummary = $this->locationAnalyticsService->getUserLocationSummary(auth()->user(), $days);
        $insights = $this->locationAnalyticsService->getLocationInsights(auth()->user());

        return view('analytics.reports', compact('tripSummary', 'locationSummary', 'insights', 'period'));
    }

    /**
     * Show carbon footprint analysis
     */
    public function carbonFootprint(Request $request)
    {
        $days = $request->get('days', 30);
        
        $carbonData = \App\Models\TripStatistic::where('user_id', auth()->id())
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

        return view('analytics.carbon-footprint', compact('carbonData', 'totalCarbon', 'totalTrips', 'days'));
    }

    /**
     * Show travel calendar
     */
    public function travelCalendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $calendarData = \App\Models\TripStatistic::where('user_id', auth()->id())
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

        return view('analytics.travel-calendar', compact('calendarData', 'month', 'year'));
    }
}