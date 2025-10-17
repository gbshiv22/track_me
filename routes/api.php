<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationSharingController;
use App\Http\Controllers\Api\GeofencingController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\OfflineSyncController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Location Sharing Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('location-sharing')->group(function () {
        Route::post('/share', [LocationSharingController::class, 'shareLocation']);
        Route::get('/active-shares', [LocationSharingController::class, 'getActiveShares']);
        Route::get('/shared-locations', [LocationSharingController::class, 'getSharedLocations']);
        Route::put('/update/{share}', [LocationSharingController::class, 'updateSharedLocation']);
        Route::delete('/revoke/{share}', [LocationSharingController::class, 'revokeShare']);
        Route::get('/realtime-updates', [LocationSharingController::class, 'getRealtimeUpdates']);
        Route::get('/stats', [LocationSharingController::class, 'getSharingStats']);
        Route::get('/can-view/{user}', [LocationSharingController::class, 'canViewLocation']);
    });

    // Geofencing Routes
    Route::prefix('geofencing')->group(function () {
        Route::post('/create', [GeofencingController::class, 'createGeofence']);
        Route::get('/list', [GeofencingController::class, 'getGeofences']);
        Route::put('/update/{geofence}', [GeofencingController::class, 'updateGeofence']);
        Route::delete('/delete/{geofence}', [GeofencingController::class, 'deleteGeofence']);
        Route::post('/check', [GeofencingController::class, 'checkGeofence']);
        Route::get('/stats', [GeofencingController::class, 'getGeofenceStats']);
        Route::patch('/toggle/{geofence}', [GeofencingController::class, 'toggleGeofence']);
    });

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/trip-summary', [AnalyticsController::class, 'getTripSummary']);
        Route::get('/trip-statistics', [AnalyticsController::class, 'getTripStatistics']);
        Route::get('/location-summary', [AnalyticsController::class, 'getLocationSummary']);
        Route::get('/heat-map', [AnalyticsController::class, 'getHeatMapData']);
        Route::get('/frequent-locations', [AnalyticsController::class, 'getFrequentLocations']);
        Route::get('/location-insights', [AnalyticsController::class, 'getLocationInsights']);
        Route::get('/weekly-report', [AnalyticsController::class, 'getWeeklyReport']);
        Route::get('/monthly-report', [AnalyticsController::class, 'getMonthlyReport']);
        Route::get('/carbon-footprint', [AnalyticsController::class, 'getCarbonFootprintSummary']);
        Route::get('/speed-analysis', [AnalyticsController::class, 'getSpeedAnalysis']);
        Route::get('/travel-calendar', [AnalyticsController::class, 'getTravelCalendar']);
    });

    // Offline Sync Routes
    Route::prefix('offline-sync')->group(function () {
        Route::post('/store-location', [OfflineSyncController::class, 'storeOfflineLocation']);
        Route::post('/sync-points', [OfflineSyncController::class, 'syncOfflinePoints']);
        Route::get('/status', [OfflineSyncController::class, 'getSyncStatus']);
        Route::get('/battery-optimization', [OfflineSyncController::class, 'getBatteryOptimization']);
        Route::get('/stats', [OfflineSyncController::class, 'getOfflineStats']);
        Route::delete('/cleanup', [OfflineSyncController::class, 'cleanupOldPoints']);
    });
});
