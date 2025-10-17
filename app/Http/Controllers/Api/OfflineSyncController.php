<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OfflineSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OfflineSyncController extends Controller
{
    protected $offlineSyncService;

    public function __construct(OfflineSyncService $offlineSyncService)
    {
        $this->offlineSyncService = $offlineSyncService;
    }

    /**
     * Store offline location point
     */
    public function storeOfflineLocation(Request $request): JsonResponse
    {
        $request->validate([
            'tracking_session_id' => 'required|exists:tracking_sessions,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'altitude' => 'nullable|numeric',
            'battery_level' => 'nullable|integer|between:0,100',
            'recorded_at' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        $locationPoint = $this->offlineSyncService->storeOfflineLocationPoint(
            $request->all(),
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Offline location stored successfully',
            'data' => $locationPoint,
        ]);
    }

    /**
     * Sync offline points
     */
    public function syncOfflinePoints(): JsonResponse
    {
        $result = $this->offlineSyncService->syncOfflinePoints(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Offline points synced successfully',
            'data' => $result,
        ]);
    }

    /**
     * Get offline sync status
     */
    public function getSyncStatus(): JsonResponse
    {
        $status = $this->offlineSyncService->getOfflineSyncStatus(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    /**
     * Get battery optimization recommendations
     */
    public function getBatteryOptimization(): JsonResponse
    {
        $recommendations = $this->offlineSyncService->optimizeBatteryUsage(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get offline statistics
     */
    public function getOfflineStats(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $stats = $this->offlineSyncService->getOfflineStats(auth()->id(), $days);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Clean up old synced points
     */
    public function cleanupOldPoints(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $deletedCount = $this->offlineSyncService->cleanupOldSyncedPoints($days);

        return response()->json([
            'success' => true,
            'message' => "Cleaned up {$deletedCount} old synced points",
            'data' => ['deleted_count' => $deletedCount],
        ]);
    }
}