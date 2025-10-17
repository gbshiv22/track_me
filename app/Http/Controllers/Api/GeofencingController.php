<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeofencingService;
use App\Models\Geofence;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeofencingController extends Controller
{
    protected $geofencingService;

    public function __construct(GeofencingService $geofencingService)
    {
        $this->geofencingService = $geofencingService;
    }

    /**
     * Create a new geofence
     */
    public function createGeofence(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:10000',
            'type' => 'required|in:circular,polygon',
            'polygon_coordinates' => 'nullable|array',
            'alert_type' => 'required|in:enter,exit,both',
            'notification_settings' => 'nullable|array',
        ]);

        $geofence = $this->geofencingService->createGeofence(auth()->user(), $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Geofence created successfully',
            'data' => $geofence,
        ]);
    }

    /**
     * Get user's geofences
     */
    public function getGeofences(): JsonResponse
    {
        $geofences = $this->geofencingService->getUserGeofences(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $geofences,
        ]);
    }

    /**
     * Update geofence
     */
    public function updateGeofence(Request $request, Geofence $geofence): JsonResponse
    {
        $this->authorize('update', $geofence);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius' => 'sometimes|integer|min:10|max:10000',
            'type' => 'sometimes|in:circular,polygon',
            'polygon_coordinates' => 'nullable|array',
            'alert_type' => 'sometimes|in:enter,exit,both',
            'notification_settings' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $updated = $this->geofencingService->updateGeofence($geofence, $request->all());

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Geofence updated successfully' : 'Failed to update geofence',
            'data' => $geofence->fresh(),
        ]);
    }

    /**
     * Delete geofence
     */
    public function deleteGeofence(Geofence $geofence): JsonResponse
    {
        $this->authorize('delete', $geofence);

        $deleted = $this->geofencingService->deleteGeofence($geofence);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Geofence deleted successfully' : 'Failed to delete geofence',
        ]);
    }

    /**
     * Check if location triggers geofence
     */
    public function checkGeofence(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $geofences = $this->geofencingService->getUserGeofences(auth()->user());
        $triggeredGeofences = [];

        foreach ($geofences as $geofence) {
            if ($geofence->containsPoint($request->latitude, $request->longitude)) {
                $triggeredGeofences[] = [
                    'id' => $geofence->id,
                    'name' => $geofence->name,
                    'alert_type' => $geofence->alert_type,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $triggeredGeofences,
        ]);
    }

    /**
     * Get geofence statistics
     */
    public function getGeofenceStats(): JsonResponse
    {
        $stats = $this->geofencingService->getGeofenceStats(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Toggle geofence active status
     */
    public function toggleGeofence(Geofence $geofence): JsonResponse
    {
        $this->authorize('update', $geofence);

        $geofence->update(['is_active' => !$geofence->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Geofence status updated',
            'data' => $geofence->fresh(),
        ]);
    }
}