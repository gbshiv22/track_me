<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationSharingService;
use App\Models\User;
use App\Models\LocationShare;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationSharingController extends Controller
{
    protected $sharingService;

    public function __construct(LocationSharingService $sharingService)
    {
        $this->sharingService = $sharingService;
    }

    /**
     * Share location with another user
     */
    public function shareLocation(Request $request): JsonResponse
    {
        $request->validate([
            'shared_with_user_id' => 'required|exists:users,id',
            'share_type' => 'required|in:realtime,trip,location',
            'tracking_session_id' => 'nullable|exists:tracking_sessions,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'expires_at' => 'nullable|date|after:now',
            'permissions' => 'nullable|array',
        ]);

        $sharedWithUser = User::findOrFail($request->shared_with_user_id);
        
        $share = $this->sharingService->shareLocation(
            auth()->user(),
            $sharedWithUser,
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Location shared successfully',
            'data' => $share->load('sharedWithUser'),
        ]);
    }

    /**
     * Get active location shares
     */
    public function getActiveShares(): JsonResponse
    {
        $shares = $this->sharingService->getActiveShares(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $shares,
        ]);
    }

    /**
     * Get locations shared with user
     */
    public function getSharedLocations(): JsonResponse
    {
        $locations = $this->sharingService->getSharedLocations(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    /**
     * Update shared location
     */
    public function updateSharedLocation(Request $request, LocationShare $share): JsonResponse
    {
        $this->authorize('update', $share);

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $updated = $this->sharingService->updateSharedLocation($share, $request->all());

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Location updated successfully' : 'Failed to update location',
        ]);
    }

    /**
     * Revoke location share
     */
    public function revokeShare(LocationShare $share): JsonResponse
    {
        $this->authorize('update', $share);

        $revoked = $this->sharingService->revokeShare($share);

        return response()->json([
            'success' => $revoked,
            'message' => $revoked ? 'Location share revoked' : 'Failed to revoke share',
        ]);
    }

    /**
     * Get real-time location updates
     */
    public function getRealtimeUpdates(): JsonResponse
    {
        $updates = $this->sharingService->getRealtimeUpdates(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $updates,
        ]);
    }

    /**
     * Get sharing statistics
     */
    public function getSharingStats(): JsonResponse
    {
        $stats = $this->sharingService->getSharingStats(auth()->user());

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Check if user can view location
     */
    public function canViewLocation(User $user): JsonResponse
    {
        $canView = $this->sharingService->canViewLocation(auth()->user(), $user);

        return response()->json([
            'success' => true,
            'can_view' => $canView,
        ]);
    }
}