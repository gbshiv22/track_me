<?php

namespace App\Services;

use App\Models\LocationShare;
use App\Models\User;
use App\Models\TrackingSession;
use Illuminate\Support\Facades\DB;

class LocationSharingService
{
    /**
     * Share location with another user
     */
    public function shareLocation(User $user, User $sharedWithUser, array $options = []): LocationShare
    {
        $shareData = [
            'user_id' => $user->id,
            'shared_with_user_id' => $sharedWithUser->id,
            'share_type' => $options['share_type'] ?? 'realtime',
            'tracking_session_id' => $options['tracking_session_id'] ?? null,
            'latitude' => $options['latitude'] ?? null,
            'longitude' => $options['longitude'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'permissions' => $options['permissions'] ?? ['view_location'],
        ];

        return LocationShare::create($shareData);
    }

    /**
     * Get active location shares for a user
     */
    public function getActiveShares(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return LocationShare::active()
            ->where('user_id', $user->id)
            ->with('sharedWithUser')
            ->get();
    }

    /**
     * Get locations shared with a user
     */
    public function getSharedLocations(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return LocationShare::active()
            ->where('shared_with_user_id', $user->id)
            ->with(['user', 'trackingSession'])
            ->get();
    }

    /**
     * Update shared location
     */
    public function updateSharedLocation(LocationShare $share, array $locationData): bool
    {
        return $share->update([
            'latitude' => $locationData['latitude'],
            'longitude' => $locationData['longitude'],
            'updated_at' => now(),
        ]);
    }

    /**
     * Revoke location share
     */
    public function revokeShare(LocationShare $share): bool
    {
        return $share->update(['is_active' => false]);
    }

    /**
     * Get real-time location updates for shared locations
     */
    public function getRealtimeUpdates(User $user): array
    {
        $sharedLocations = $this->getSharedLocations($user);
        $updates = [];

        foreach ($sharedLocations as $share) {
            if ($share->share_type === 'realtime' && $share->trackingSession) {
                $latestLocation = $share->trackingSession->latestLocation;
                if ($latestLocation) {
                    $updates[] = [
                        'user_id' => $share->user_id,
                        'user_name' => $share->user->name,
                        'latitude' => $latestLocation->latitude,
                        'longitude' => $latestLocation->longitude,
                        'recorded_at' => $latestLocation->recorded_at,
                        'speed' => $latestLocation->speed,
                        'heading' => $latestLocation->heading,
                    ];
                }
            }
        }

        return $updates;
    }

    /**
     * Check if user can view location
     */
    public function canViewLocation(User $viewer, User $targetUser): bool
    {
        return LocationShare::active()
            ->where('user_id', $targetUser->id)
            ->where('shared_with_user_id', $viewer->id)
            ->exists();
    }

    /**
     * Get sharing statistics
     */
    public function getSharingStats(User $user): array
    {
        $totalShares = LocationShare::where('user_id', $user->id)->count();
        $activeShares = LocationShare::active()->where('user_id', $user->id)->count();
        $receivedShares = LocationShare::active()->where('shared_with_user_id', $user->id)->count();

        return [
            'total_shares' => $totalShares,
            'active_shares' => $activeShares,
            'received_shares' => $receivedShares,
        ];
    }
}
