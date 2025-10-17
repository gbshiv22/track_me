<?php

namespace App\Services;

use App\Models\Geofence;
use App\Models\User;
use App\Models\LocationPoint;
use Illuminate\Support\Facades\Log;

class GeofencingService
{
    /**
     * Create a new geofence
     */
    public function createGeofence(User $user, array $data): Geofence
    {
        return Geofence::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'radius' => $data['radius'],
            'type' => $data['type'] ?? 'circular',
            'polygon_coordinates' => $data['polygon_coordinates'] ?? null,
            'alert_type' => $data['alert_type'] ?? 'both',
            'notification_settings' => $data['notification_settings'] ?? ['push'],
        ]);
    }

    /**
     * Check if a location point triggers any geofence alerts
     */
    public function checkGeofenceAlerts(LocationPoint $locationPoint): array
    {
        $alerts = [];
        $user = $locationPoint->trackingSession->user;
        
        $geofences = Geofence::active()
            ->where('user_id', $user->id)
            ->get();

        foreach ($geofences as $geofence) {
            $isInside = $geofence->containsPoint($locationPoint->latitude, $locationPoint->longitude);
            $alert = $this->checkGeofenceAlert($geofence, $isInside, $locationPoint);
            
            if ($alert) {
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Check individual geofence alert
     */
    private function checkGeofenceAlert(Geofence $geofence, bool $isInside, LocationPoint $locationPoint): ?array
    {
        $alertType = $geofence->alert_type;
        
        // Check if this is a new state change
        $previousState = $this->getPreviousGeofenceState($geofence, $locationPoint);
        
        if ($alertType === 'enter' && $isInside && !$previousState) {
            return $this->createAlert($geofence, 'enter', $locationPoint);
        }
        
        if ($alertType === 'exit' && !$isInside && $previousState) {
            return $this->createAlert($geofence, 'exit', $locationPoint);
        }
        
        if ($alertType === 'both') {
            if ($isInside && !$previousState) {
                return $this->createAlert($geofence, 'enter', $locationPoint);
            }
            if (!$isInside && $previousState) {
                return $this->createAlert($geofence, 'exit', $locationPoint);
            }
        }

        return null;
    }

    /**
     * Create geofence alert
     */
    private function createAlert(Geofence $geofence, string $type, LocationPoint $locationPoint): array
    {
        $alert = [
            'geofence_id' => $geofence->id,
            'geofence_name' => $geofence->name,
            'alert_type' => $type,
            'latitude' => $locationPoint->latitude,
            'longitude' => $locationPoint->longitude,
            'recorded_at' => $locationPoint->recorded_at,
            'user_id' => $geofence->user_id,
        ];

        // Send notifications based on settings
        $this->sendGeofenceNotification($geofence, $alert);

        return $alert;
    }

    /**
     * Get previous geofence state (simplified - in real app, store state)
     */
    private function getPreviousGeofenceState(Geofence $geofence, LocationPoint $locationPoint): bool
    {
        // This is simplified - in a real app, you'd store the previous state
        // For now, we'll assume this is the first check
        return false;
    }

    /**
     * Send geofence notification
     */
    private function sendGeofenceNotification(Geofence $geofence, array $alert): void
    {
        $settings = $geofence->notification_settings ?? ['push'];
        
        foreach ($settings as $type) {
            switch ($type) {
                case 'push':
                    $this->sendPushNotification($geofence->user, $alert);
                    break;
                case 'email':
                    $this->sendEmailNotification($geofence->user, $alert);
                    break;
                case 'sms':
                    $this->sendSmsNotification($geofence->user, $alert);
                    break;
            }
        }
    }

    /**
     * Send push notification
     */
    private function sendPushNotification(User $user, array $alert): void
    {
        // Implement push notification logic
        Log::info('Geofence alert sent to user', [
            'user_id' => $user->id,
            'alert' => $alert,
        ]);
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, array $alert): void
    {
        // Implement email notification logic
        Log::info('Geofence email alert sent to user', [
            'user_id' => $user->id,
            'alert' => $alert,
        ]);
    }

    /**
     * Send SMS notification
     */
    private function sendSmsNotification(User $user, array $alert): void
    {
        // Implement SMS notification logic
        Log::info('Geofence SMS alert sent to user', [
            'user_id' => $user->id,
            'alert' => $alert,
        ]);
    }

    /**
     * Get user's geofences
     */
    public function getUserGeofences(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Geofence::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update geofence
     */
    public function updateGeofence(Geofence $geofence, array $data): bool
    {
        return $geofence->update($data);
    }

    /**
     * Delete geofence
     */
    public function deleteGeofence(Geofence $geofence): bool
    {
        return $geofence->delete();
    }

    /**
     * Get geofence statistics
     */
    public function getGeofenceStats(User $user): array
    {
        $totalGeofences = Geofence::where('user_id', $user->id)->count();
        $activeGeofences = Geofence::active()->where('user_id', $user->id)->count();
        
        return [
            'total_geofences' => $totalGeofences,
            'active_geofences' => $activeGeofences,
        ];
    }
}
