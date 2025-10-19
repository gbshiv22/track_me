<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Web\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'Test route is working!';
});

Route::get('/', function () {
    return 'Hello World! Track Me is working!';
});

// Health check route for Render
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'app' => 'Track Me'
    ]);
});

Route::middleware('auth')->group(function () {
    // Dashboard - show analytics and statistics
    Route::get('/dashboard', [TrackingController::class, 'dashboard'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Tracking routes
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::post('/tracking/start', [TrackingController::class, 'start'])->name('tracking.start');
    Route::post('/tracking/stop', [TrackingController::class, 'stop'])->name('tracking.stop');
    Route::post('/tracking/location', [TrackingController::class, 'storeLocation'])->name('tracking.location');
    Route::get('/tracking/active', [TrackingController::class, 'getActiveSession'])->name('tracking.active');
    
    // Routes viewing
    Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
    Route::get('/routes/{id}', [RouteController::class, 'show'])->name('routes.show');
    Route::delete('/routes/{id}', [RouteController::class, 'destroy'])->name('routes.destroy');
    
    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/trip-statistics', [AnalyticsController::class, 'tripStatistics'])->name('trip-statistics');
        Route::get('/heat-map', [AnalyticsController::class, 'heatMap'])->name('heat-map');
        Route::get('/reports', [AnalyticsController::class, 'reports'])->name('reports');
        Route::get('/carbon-footprint', [AnalyticsController::class, 'carbonFootprint'])->name('carbon-footprint');
        Route::get('/travel-calendar', [AnalyticsController::class, 'travelCalendar'])->name('travel-calendar');
    });
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/live-tracking', [AdminController::class, 'liveTracking'])->name('live-tracking');
        Route::get('/active-sessions', [AdminController::class, 'getActiveSessions'])->name('active-sessions');
        Route::get('/all-routes', [AdminController::class, 'allRoutes'])->name('all-routes');
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    });
});

require __DIR__.'/auth.php';
