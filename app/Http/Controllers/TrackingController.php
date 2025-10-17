<?php

namespace App\Http\Controllers;

use App\Models\TrackingSession;
use App\Models\LocationPoint;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrackingController extends Controller
{
    /**
     * Show the analytics dashboard
     */
    public function dashboard()
    {
        // Get analytics data for dashboard
        $tripSummary = \App\Models\TripStatistic::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('
                COUNT(*) as total_trips,
                SUM(total_distance) as total_distance,
                AVG(average_speed) as average_speed,
                SUM(carbon_footprint) as total_carbon_footprint
            ')
            ->first();

        $recentTrips = auth()->user()->trackingSessions()
            ->where('status', 'completed')
            ->orderBy('stopped_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('tripSummary', 'recentTrips'));
    }

    /**
     * Show the tracking page with map
     */
    public function index()
    {
        $activeSession = auth()->user()->trackingSessions()
            ->where('status', 'active')
            ->with('locationPoints')
            ->first();

        // Get completed routes for the routes list with pagination
        $recentTrips = auth()->user()->trackingSessions()
            ->where('status', 'completed')
            ->orderBy('stopped_at', 'desc')
            ->paginate(6); // Show 6 routes per page

        return view('tracking.index', compact('activeSession', 'recentTrips'));
    }

    /**
     * Start a new tracking session
     */
    public function start(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        // Check if there's already an active session
        $existingSession = auth()->user()->trackingSessions()
            ->where('status', 'active')
            ->first();

        if ($existingSession) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active tracking session',
                'session' => $existingSession
            ], 400);
        }

        // Create new tracking session
        $session = TrackingSession::create([
            'user_id' => auth()->id(),
            'start_latitude' => $request->latitude,
            'start_longitude' => $request->longitude,
            'started_at' => Carbon::now(),
            'status' => 'active',
        ]);

        // Create first location point
        LocationPoint::create([
            'tracking_session_id' => $session->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'recorded_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tracking started successfully',
            'session' => $session
        ]);
    }

    /**
     * Stop the active tracking session
     */
    public function stop(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:tracking_sessions,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $session = TrackingSession::where('id', $request->session_id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        // Update session with end location
        $session->update([
            'end_latitude' => $request->latitude,
            'end_longitude' => $request->longitude,
            'stopped_at' => Carbon::now(),
            'status' => 'completed',
        ]);

        // Create final location point
        LocationPoint::create([
            'tracking_session_id' => $session->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'recorded_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tracking stopped successfully',
            'session' => $session->load('locationPoints')
        ]);
    }

    /**
     * Store a location point for active session
     */
    public function storeLocation(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:tracking_sessions,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $session = TrackingSession::where('id', $request->session_id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        $locationPoint = LocationPoint::create([
            'tracking_session_id' => $session->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'recorded_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location stored successfully',
            'location' => $locationPoint
        ]);
    }

    /**
     * Get active tracking session
     */
    public function getActiveSession()
    {
        $session = auth()->user()->trackingSessions()
            ->where('status', 'active')
            ->with('locationPoints')
            ->first();

        return response()->json([
            'success' => true,
            'session' => $session
        ]);
    }
}
