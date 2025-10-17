<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\GeofencingService;
use App\Models\Geofence;
use Illuminate\Http\Request;

class GeofencingController extends Controller
{
    protected $geofencingService;

    public function __construct(GeofencingService $geofencingService)
    {
        $this->geofencingService = $geofencingService;
    }

    /**
     * Show geofencing dashboard
     */
    public function index()
    {
        $geofences = $this->geofencingService->getUserGeofences(auth()->user());
        $stats = $this->geofencingService->getGeofenceStats(auth()->user());

        return view('geofencing.index', compact('geofences', 'stats'));
    }

    /**
     * Show create geofence form
     */
    public function create()
    {
        return view('geofencing.create');
    }

    /**
     * Store new geofence
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:10000',
            'type' => 'required|in:circular,polygon',
            'alert_type' => 'required|in:enter,exit,both',
            'notification_settings' => 'nullable|array',
        ]);

        $geofence = $this->geofencingService->createGeofence(auth()->user(), $request->all());

        return redirect()->route('geofencing.index')
            ->with('success', 'Geofence created successfully!');
    }

    /**
     * Show edit geofence form
     */
    public function edit(Geofence $geofence)
    {
        $this->authorize('update', $geofence);
        
        return view('geofencing.edit', compact('geofence'));
    }

    /**
     * Update geofence
     */
    public function update(Request $request, Geofence $geofence)
    {
        $this->authorize('update', $geofence);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes|integer|min:10|max:10000',
            'type' => 'sometimes|in:circular,polygon',
            'alert_type' => 'sometimes|in:enter,exit,both',
            'notification_settings' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $updated = $this->geofencingService->updateGeofence($geofence, $request->all());

        return redirect()->route('geofencing.index')
            ->with('success', $updated ? 'Geofence updated successfully!' : 'Failed to update geofence');
    }

    /**
     * Delete geofence
     */
    public function destroy(Geofence $geofence)
    {
        $this->authorize('delete', $geofence);

        $deleted = $this->geofencingService->deleteGeofence($geofence);

        return redirect()->route('geofencing.index')
            ->with('success', $deleted ? 'Geofence deleted successfully!' : 'Failed to delete geofence');
    }

    /**
     * Toggle geofence active status
     */
    public function toggle(Geofence $geofence)
    {
        $this->authorize('update', $geofence);

        $geofence->update(['is_active' => !$geofence->is_active]);

        return redirect()->route('geofencing.index')
            ->with('success', 'Geofence status updated!');
    }
}