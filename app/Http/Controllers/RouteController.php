<?php

namespace App\Http\Controllers;

use App\Models\TrackingSession;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    /**
     * Display all user's routes
     */
    public function index()
    {
        $sessions = auth()->user()->trackingSessions()
            ->where('status', 'completed')
            ->orderBy('started_at', 'desc')
            ->paginate(20);

        return view('routes.index', compact('sessions'));
    }

    /**
     * Show a specific route with map
     */
    public function show($id)
    {
        $session = TrackingSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('locationPoints')
            ->firstOrFail();

        // Calculate statistics
        $duration = $session->started_at && $session->stopped_at 
            ? $session->started_at->diffInMinutes($session->stopped_at)
            : 0;

        $totalDistance = $this->calculateTotalDistance($session->locationPoints);

        return view('routes.show', compact('session', 'duration', 'totalDistance'));
    }

    /**
     * Calculate total distance traveled (in kilometers)
     */
    private function calculateTotalDistance($locationPoints)
    {
        if ($locationPoints->count() < 2) {
            return 0;
        }

        $totalDistance = 0;
        
        for ($i = 0; $i < $locationPoints->count() - 1; $i++) {
            $point1 = $locationPoints[$i];
            $point2 = $locationPoints[$i + 1];
            
            $totalDistance += $this->haversineDistance(
                $point1->latitude, 
                $point1->longitude,
                $point2->latitude, 
                $point2->longitude
            );
        }

        return round($totalDistance, 2);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Delete a route
     */
    public function destroy($id)
    {
        $session = TrackingSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $session->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Route deleted successfully');
    }
}
