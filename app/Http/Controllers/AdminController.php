<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TrackingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show live tracking dashboard (admin only)
     */
    public function liveTracking()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Get all active tracking sessions with latest location
        $activeSessions = TrackingSession::where('status', 'active')
            ->with(['user', 'latestLocation'])
            ->get();

        return view('admin.live-tracking', compact('activeSessions'));
    }

    /**
     * Get active sessions data (for AJAX refresh)
     */
    public function getActiveSessions()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $activeSessions = TrackingSession::where('status', 'active')
            ->with(['user', 'latestLocation'])
            ->get()
            ->map(function ($session) {
                $latest = $session->latestLocation;
                return [
                    'id' => $session->id,
                    'user_name' => $session->user->name,
                    'started_at' => $session->started_at->format('Y-m-d H:i:s'),
                    'latitude' => $latest ? $latest->latitude : $session->start_latitude,
                    'longitude' => $latest ? $latest->longitude : $session->start_longitude,
                    'recorded_at' => $latest ? $latest->recorded_at->format('Y-m-d H:i:s') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'sessions' => $activeSessions
        ]);
    }

    /**
     * Show all users and their routes (admin only)
     */
    public function allRoutes()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $sessions = TrackingSession::with('user')
            ->where('status', 'completed')
            ->orderBy('started_at', 'desc')
            ->paginate(20);

        return view('admin.all-routes', compact('sessions'));
    }

    /**
     * Show statistics dashboard
     */
    public function statistics()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Total users
        $totalUsers = User::count();

        // Total completed sessions
        $totalSessions = TrackingSession::where('status', 'completed')->count();

        // Active sessions right now
        $activeSessions = TrackingSession::where('status', 'active')->count();

        // Most active user
        $mostActiveUser = User::withCount(['trackingSessions' => function ($query) {
            $query->where('status', 'completed');
        }])
        ->orderBy('tracking_sessions_count', 'desc')
        ->first();

        // Recent sessions
        $recentSessions = TrackingSession::with('user')
            ->where('status', 'completed')
            ->orderBy('stopped_at', 'desc')
            ->limit(10)
            ->get();

        // Sessions by day (last 7 days)
        $sessionsByDay = TrackingSession::where('status', 'completed')
            ->where('started_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(started_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Analytics data for current user
        $tripSummary = \App\Models\TripStatistic::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('
                COUNT(*) as total_trips,
                SUM(total_distance) as total_distance,
                AVG(average_speed) as average_speed,
                SUM(carbon_footprint) as total_carbon_footprint,
                SUM(total_duration) as total_duration
            ')
            ->first();

        // Transport mode breakdown
        $transportModes = \App\Models\TripStatistic::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(30))
            ->select('transport_mode', DB::raw('COUNT(*) as count'))
            ->groupBy('transport_mode')
            ->get();

        return view('admin.statistics', compact(
            'totalUsers',
            'totalSessions',
            'activeSessions',
            'mostActiveUser',
            'recentSessions',
            'sessionsByDay',
            'tripSummary',
            'transportModes'
        ));
    }
}
