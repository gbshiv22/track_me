<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Track My Location') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Map Container -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Live Tracking Map</h3>
                    <div class="flex space-x-2">
                        <button id="locate-me" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Locate Me
                        </button>
                        <button id="fullscreen-toggle" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            Fullscreen
                        </button>
                        <button id="reset-map" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Map
                        </button>
                    </div>
                </div>
                <div id="map-wrapper" class="relative">
                    <div id="map" class="w-full h-96 rounded-lg border border-gray-200">
                        <!-- Compass -->
                        <div id="compass" class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-2 hidden" style="z-index: 1000;">
                            <div class="w-12 h-12 relative">
                                <div class="absolute inset-0 rounded-full border-2 border-gray-300"></div>
                                <div id="compass-needle" class="absolute inset-2 rounded-full bg-red-500 transform transition-transform duration-300"></div>
                                <div class="absolute top-1 left-1/2 transform -translate-x-1/2 text-xs font-bold text-gray-600">N</div>
                            </div>
                        </div>
                        <!-- Map Controls -->
                        <div class="absolute top-4 left-4 bg-white rounded-lg shadow-lg p-1" style="z-index: 1000;">
                            <button id="zoom-in" class="block w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                            <button id="zoom-out" class="block w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                </svg>
                            </button>
                        </div>
                        <!-- Fullscreen Controls (Hidden by default) -->
                        <div id="fullscreen-controls" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg p-4 hidden" style="z-index: 1000;">
                            <div class="flex items-center space-x-3">
                                <button id="fs-locate-me" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Locate Me
                                </button>
                                <button id="fs-track-button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Start Tracking
                                </button>
                                <button id="fs-exit-fullscreen" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M15 9v4.5M15 9h4.5M15 9l5.25-5.25M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 15v-4.5M15 15h4.5m0 0l5.25 5.25"/>
                                    </svg>
                                    Exit Fullscreen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            
            <!-- Status Messages -->
            <div id="status-message" class="mb-6 hidden">
                <div class="p-4 rounded-md" id="status-content"></div>
            </div>

            <!-- Tracking Status -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                <div class="flex items-center space-x-3">
                    <div id="status-indicator" class="status-indicator status-inactive"></div>
                    <span id="status-text" class="text-sm font-medium text-gray-600">Not Tracking</span>
                </div>
            </div>

            <!-- Session Info -->
            <div id="session-info" class="mb-8 hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Session</h3>
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Started:</span>
                        <span id="session-started" class="text-sm text-gray-900"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Points Recorded:</span>
                        <span id="points-count" class="text-sm font-medium text-blue-600">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Current Location:</span>
                        <span id="current-location" class="text-sm text-gray-900">-</span>
                    </div>
                </div>
            </div>

            <!-- Track Me Button -->
            <div class="text-center mb-8">
                <div class="relative inline-block w-full max-w-xs">
                    <button id="track-button" class="btn btn-success btn-xl w-full flex items-center justify-center" style="min-width:150px;">
                        <svg id="spinner" class="animate-spin h-5 w-5 mr-2 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span id="track-btn-text">Start Tracking</span>
                    </button>
                </div>
            </div>

            <!-- Instructions -->
            <div class="alert alert-info">
                <h4 class="font-medium mb-3">How it works:</h4>
                <ul class="list-disc list-inside space-y-2 text-sm">
                    <li>Click "Start Tracking" to begin recording your location</li>
                    <li>Your location will be recorded every {{ config('tracking.interval', 10) }} seconds</li>
                    <li>Click "Stop Tracking" when you reach your destination</li>
                    <li>View your route in "My Routes" section</li>
                </ul>
            </div>

        </div>

        <!-- Routes List -->
        @if($recentTrips && $recentTrips->count() > 0)
        <div class="bg-white overflow-hidden shadow rounded-lg mt-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">My Recent Routes</h3>
                    <span class="text-sm text-gray-500">{{ $recentTrips->count() }} routes</span>
                </div>
                
                <!-- Routes Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @foreach($recentTrips as $route)
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Route #{{ $route->id }}</p>
                                    <p class="text-xs text-gray-500">{{ $route->started_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($route->stopped_at)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Active
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Started:</span>
                                <span class="text-gray-900">{{ $route->started_at->format('H:i') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Duration:</span>
                                <span class="text-gray-900">
                                    @if($route->stopped_at)
                                        {{ $route->started_at->diffInMinutes($route->stopped_at) }} min
                                    @else
                                        Active
                                    @endif
                                </span>
                            </div>
                            @if($route->locationPoints && $route->locationPoints->count() > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Points:</span>
                                <span class="text-gray-900">{{ $route->locationPoints->count() }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('routes.show', $route->id) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Route
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($recentTrips->hasPages())
                <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        @if($recentTrips->onFirstPage())
                            <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">
                                Previous
                            </span>
                        @else
                            <a href="{{ $recentTrips->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        @endif
                        
                        @if($recentTrips->hasMorePages())
                            <a href="{{ $recentTrips->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">
                                Next
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ $recentTrips->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $recentTrips->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $recentTrips->total() }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                @if($recentTrips->onFirstPage())
                                    <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $recentTrips->previousPageUrl() }}" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @endif
                                
                                @foreach($recentTrips->getUrlRange(1, $recentTrips->lastPage()) as $page => $url)
                                    @if($page == $recentTrips->currentPage())
                                        <span class="relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">{{ $page }}</a>
                                    @endif
                                @endforeach
                                
                                @if($recentTrips->hasMorePages())
                                    <a href="{{ $recentTrips->nextPageUrl() }}" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="mt-4 text-center">
                    <a href="{{ route('routes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        View All Routes
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Configuration
        const TRACKING_INTERVAL = {{ config('tracking.interval', 10) }} * 1000; // Convert to milliseconds
        const CSRF_TOKEN = '{{ csrf_token() }}';
        
        // State variables
        let isTracking = false;
        let trackingInterval = null;
        let sessionId = null;
        let pointsCount = 0;
        let map = null;
        let currentMarker = null;
        let pathPolyline = null;
        let pathCoordinates = [];
        let startMarker = null;
        let endMarker = null;
        let routeCounter = 0; // Track route number (A, B, C, D, etc.)
        let allRoutes = []; // Store all completed routes

        // DOM elements
        const trackButton = document.getElementById('track-button');
        const statusIndicator = document.getElementById('status-indicator');
        const statusText = document.getElementById('status-text');
        const sessionInfo = document.getElementById('session-info');
        const sessionStarted = document.getElementById('session-started');
        const pointsCountEl = document.getElementById('points-count');
        const currentLocation = document.getElementById('current-location');
        const statusMessage = document.getElementById('status-message');
        const statusContent = document.getElementById('status-content');
        const resetMapButton = document.getElementById('reset-map');
        const locateMeButton = document.getElementById('locate-me');
        const fullscreenToggleButton = document.getElementById('fullscreen-toggle');
        const compass = document.getElementById('compass');
        const compassNeedle = document.getElementById('compass-needle');
        const zoomInButton = document.getElementById('zoom-in');
        const zoomOutButton = document.getElementById('zoom-out');
        const mapContainer = document.getElementById('map');
        const fullscreenControls = document.getElementById('fullscreen-controls');
        const fsLocateMeButton = document.getElementById('fs-locate-me');
        const fsTrackButton = document.getElementById('fs-track-button');
        const fsExitFullscreenButton = document.getElementById('fs-exit-fullscreen');

        // Initialize map
        function initMap() {
            map = L.map('map').setView([0, 0], 2); // Default view
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Try to get user's current location
            locateUser();
        }

        // Locate user function
        function locateUser() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 15);
                    
                    // Add current location marker
                    if (!currentMarker) {
                        currentMarker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                className: 'current-location-marker',
                                html: '<div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-lg"></div>',
                                iconSize: [16, 16],
                                iconAnchor: [8, 8]
                            })
                        }).addTo(map);
                        currentMarker.bindPopup('Your current location').openPopup();
                    } else {
                        currentMarker.setLatLng([lat, lng]);
                    }
                    
                    // Update compass
                    updateCompass(position.coords.heading);
                    
                    showMessage('Location found!', 'success');
                }, function(error) {
                    showMessage('Unable to get your location: ' + error.message, 'error');
                });
            } else {
                showMessage('Geolocation is not supported by your browser', 'error');
            }
        }

        // Update compass
        function updateCompass(heading) {
            if (heading !== null && heading !== undefined) {
                compass.classList.remove('hidden');
                compassNeedle.style.transform = `rotate(${heading}deg)`;
            }
        }

        // Fullscreen functionality
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                // Enter fullscreen
                mapContainer.requestFullscreen().then(() => {
                    // Show fullscreen controls
                    fullscreenControls.classList.remove('hidden');
                    
                    // Update map size
                    setTimeout(() => {
                        map.invalidateSize();
                    }, 200);
                    
                    // Update button text
                    fullscreenToggleButton.innerHTML = `
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M15 9v4.5M15 9h4.5M15 9l5.25-5.25M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 15v-4.5M15 15h4.5m0 0l5.25 5.25"/>
                        </svg>
                        Exit Fullscreen
                    `;
                    
                    // Sync tracking button state
                    updateFullscreenTrackingButton();
                    
                    showMessage('Map entered fullscreen mode - Use controls at bottom', 'success');
                }).catch(err => {
                    showMessage('Error entering fullscreen: ' + err.message, 'error');
                    console.error('Fullscreen error:', err);
                });
            } else {
                // Exit fullscreen
                document.exitFullscreen();
            }
        }

        // Listen for fullscreen changes
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                // Exited fullscreen
                fullscreenControls.classList.add('hidden');
                
                // Update map size
                setTimeout(() => {
                    map.invalidateSize();
                }, 200);
                
                // Update button text
                fullscreenToggleButton.innerHTML = `
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    Fullscreen
                `;
                
                showMessage('Exited fullscreen mode', 'success');
            }
        });

        // Update fullscreen tracking button to match main button
        function updateFullscreenTrackingButton() {
            if (isTracking) {
                fsTrackButton.innerHTML = `
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Stop Tracking
                `;
                fsTrackButton.className = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
            } else {
                fsTrackButton.innerHTML = `
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Start Tracking
                `;
                fsTrackButton.className = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
            }
        }

        // Reset map
        function resetMap() {
            // Clear all markers and polylines
            if (currentMarker) {
                map.removeLayer(currentMarker);
                currentMarker = null;
            }
            if (startMarker) {
                map.removeLayer(startMarker);
                startMarker = null;
            }
            if (endMarker) {
                map.removeLayer(endMarker);
                endMarker = null;
            }
            if (pathPolyline) {
                map.removeLayer(pathPolyline);
                pathPolyline = null;
            }
            
            // Clear all completed routes
            allRoutes.forEach(route => {
                if (route.startMarker) map.removeLayer(route.startMarker);
                if (route.endMarker) map.removeLayer(route.endMarker);
                if (route.polyline) map.removeLayer(route.polyline);
            });
            
            // Reset all variables
            pathCoordinates = [];
            allRoutes = [];
            routeCounter = 0;
            
            // Hide compass
            compass.classList.add('hidden');
            
            // Reset to default view
            map.setView([0, 0], 2);
            
            showMessage('Map reset successfully - All routes cleared', 'success');
        }

        // Check for active session on load
        checkActiveSession();

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });

        // Button click handlers
        // Overwrite the tracking button click listener to show spinner during AJAX
        const spinner = document.getElementById('spinner');
        const trackBtnText = document.getElementById('track-btn-text');
        function setLoading(isLoading) {
            if (isLoading) {
                spinner.classList.remove('hidden');
                trackBtnText.classList.add('opacity-50');
                trackButton.disabled = true;
            } else {
                spinner.classList.add('hidden');
                trackBtnText.classList.remove('opacity-50');
                trackButton.disabled = false;
            }
        }
        // Update event listeners for Start/Stop Tracking to show spinner
        trackButton.addEventListener('click', function() {
            if (!isTracking) {
                setLoading(true);
                startTracking();
            } else {
                setLoading(true);
                stopTracking();
            }
        });
        // Overwrite success/failure callbacks:
        function afterTrackRequestComplete() { setLoading(false); }
        // Patch into .then/.catch of AJAX in startTracking/stopTracking
        // In startTracking:
        // ...
        .then(data => { /* ... */ afterTrackRequestComplete(); })
        .catch(err => { /* ... */ afterTrackRequestComplete(); });
        // Same for stopTracking
        // ...
        resetMapButton.addEventListener('click', resetMap);
        locateMeButton.addEventListener('click', locateUser);
        fullscreenToggleButton.addEventListener('click', toggleFullscreen);
        zoomInButton.addEventListener('click', function() {
            map.zoomIn();
        });
        zoomOutButton.addEventListener('click', function() {
            map.zoomOut();
        });

        // Fullscreen control event listeners
        fsLocateMeButton.addEventListener('click', function() {
            console.log('Fullscreen locate me clicked');
            locateUser();
        });
        fsTrackButton.addEventListener('click', function() {
            console.log('Fullscreen track button clicked, isTracking:', isTracking);
            if (!isTracking) {
                startTracking();
            } else {
                stopTracking();
            }
        });
        fsExitFullscreenButton.addEventListener('click', function() {
            console.log('Fullscreen exit clicked');
            if (document.fullscreenElement) {
                document.exitFullscreen();
            }
        });

        // Debug: Log when fullscreen controls are shown/hidden
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    console.log('Fullscreen controls visibility changed:', !fullscreenControls.classList.contains('hidden'));
                }
            });
        });
        observer.observe(fullscreenControls, { attributes: true });

        // Check if there's already an active session
        function checkActiveSession() {
            fetch('{{ route("tracking.active") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.session) {
                    // Resume active session
                    sessionId = data.session.id;
                    pointsCount = data.session.location_points ? data.session.location_points.length : 0;
                    
                    // Plot existing path
                    if (data.session.location_points && data.session.location_points.length > 0) {
                        const coordinates = data.session.location_points.map(point => [point.latitude, point.longitude]);
                        pathCoordinates = coordinates;
                        
                        // Get current route letter based on existing routes
                        const startLetter = String.fromCharCode(65 + routeCounter);
                        
                        // Add start marker
                        if (coordinates.length > 0) {
                            startMarker = L.marker(coordinates[0], {
                                icon: L.divIcon({
                                    className: 'start-marker',
                                    html: `<div class="w-6 h-6 bg-green-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white font-bold text-xs">${startLetter}</div>`,
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            }).addTo(map);
                            startMarker.bindPopup(`Start Location (${startLetter})`);
                        }
                        
                        // Add path polyline
                        if (coordinates.length > 1) {
                            pathPolyline = L.polyline(coordinates, {
                                color: 'blue',
                                weight: 3,
                                opacity: 0.7
                            }).addTo(map);
                        }
                        
                        // Center map on path
                        if (coordinates.length > 0) {
                            map.fitBounds(L.polyline(coordinates).getBounds());
                        }
                    }
                    
                    resumeTracking();
                    sessionStarted.textContent = new Date(data.session.started_at).toLocaleString();
                    pointsCountEl.textContent = pointsCount;
                }
            })
            .catch(error => console.error('Error checking active session:', error));
        }

        // Start tracking
        function startTracking() {
            if (!navigator.geolocation) {
                showMessage('Geolocation is not supported by your browser', 'error');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    const data = {
                        latitude: lat,
                        longitude: lng,
                        accuracy: position.coords.accuracy
                    };

                    fetch('{{ route("tracking.start") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            sessionId = data.session.id;
                            pointsCount = 1;
                            
                            // Get next route letter
                            const startLetter = String.fromCharCode(65 + routeCounter); // A, B, C, D, etc.
                            
                            // Add start marker with sequential letter
                            startMarker = L.marker([lat, lng], {
                                icon: L.divIcon({
                                    className: 'start-marker',
                                    html: `<div class="w-6 h-6 bg-green-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white font-bold text-xs">${startLetter}</div>`,
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            }).addTo(map);
                            startMarker.bindPopup(`Start Location (${startLetter})`);
                            
                            // Initialize path
                            pathCoordinates = [[lat, lng]];
                            
                            // Center map on start location
                            map.setView([lat, lng], 15);
                            
                            resumeTracking();
                            sessionStarted.textContent = new Date().toLocaleString();
                            pointsCountEl.textContent = pointsCount;
                            showMessage(`Tracking started successfully! Route ${startLetter}`, 'success');
                        } else {
                            showMessage(data.message || 'Failed to start tracking', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('Error starting tracking', 'error');
                    });
                },
                function(error) {
                    showMessage('Unable to get your location: ' + error.message, 'error');
                }
            );
        }

        // Resume tracking (for active sessions)
        function resumeTracking() {
            isTracking = true;
            updateUI();
            
            // Start interval to send location
            trackingInterval = setInterval(sendLocation, TRACKING_INTERVAL);
        }

        // Send location to server
        function sendLocation() {
            if (!navigator.geolocation) return;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    const data = {
                        session_id: sessionId,
                        latitude: lat,
                        longitude: lng,
                        accuracy: position.coords.accuracy
                    };

                    fetch('{{ route("tracking.location") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            pointsCount++;
                            pointsCountEl.textContent = pointsCount;
                            currentLocation.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                            
                            // Add point to path
                            pathCoordinates.push([lat, lng]);
                            
                            // Update or create polyline
                            if (pathPolyline) {
                                map.removeLayer(pathPolyline);
                            }
                            pathPolyline = L.polyline(pathCoordinates, {
                                color: 'blue',
                                weight: 3,
                                opacity: 0.7
                            }).addTo(map);
                            
                            // Update current location marker
                            if (currentMarker) {
                                currentMarker.setLatLng([lat, lng]);
                            } else {
                                currentMarker = L.marker([lat, lng], {
                                    icon: L.divIcon({
                                        className: 'current-location-marker',
                                        html: '<div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-lg"></div>',
                                        iconSize: [16, 16],
                                        iconAnchor: [8, 8]
                                    })
                                }).addTo(map);
                                currentMarker.bindPopup('Your current location');
                            }
                            
                            // Update compass
                            updateCompass(position.coords.heading);
                        }
                    })
                    .catch(error => console.error('Error sending location:', error));
                },
                function(error) {
                    console.error('Geolocation error:', error);
                }
            );
        }

        // Stop tracking
        function stopTracking() {
            if (!navigator.geolocation) return;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    const data = {
                        session_id: sessionId,
                        latitude: lat,
                        longitude: lng,
                        accuracy: position.coords.accuracy
                    };

                    fetch('{{ route("tracking.stop") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            clearInterval(trackingInterval);
                            isTracking = false;
                            
                            // Get current route letters
                            const startLetter = String.fromCharCode(65 + routeCounter);
                            const endLetter = String.fromCharCode(66 + routeCounter); // Next letter for end
                            
                            // Add end marker with next sequential letter
                            endMarker = L.marker([lat, lng], {
                                icon: L.divIcon({
                                    className: 'end-marker',
                                    html: `<div class="w-6 h-6 bg-red-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white font-bold text-xs">${endLetter}</div>`,
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                })
                            }).addTo(map);
                            endMarker.bindPopup(`End Location (${endLetter})`);
                            
                            // Save completed route
                            const completedRoute = {
                                startLetter: startLetter,
                                endLetter: endLetter,
                                startMarker: startMarker,
                                endMarker: endMarker,
                                polyline: pathPolyline,
                                coordinates: [...pathCoordinates]
                            };
                            allRoutes.push(completedRoute);
                            
                            // Remove current location marker
                            if (currentMarker) {
                                map.removeLayer(currentMarker);
                                currentMarker = null;
                            }
                            
                            // Increment route counter for next route
                            routeCounter += 2; // Increment by 2 (A->B, C->D, E->F, etc.)
                            
                            // Reset current session variables
                            sessionId = null;
                            pointsCount = 0;
                            startMarker = null;
                            endMarker = null;
                            pathPolyline = null;
                            pathCoordinates = [];
                            
                            updateUI();
                            showMessage(`Tracking stopped successfully! Route ${startLetter}→${endLetter} completed. View your route in "My Routes"`, 'success');
                            sessionInfo.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('Error stopping tracking', 'error');
                    });
                },
                function(error) {
                    showMessage('Unable to get your location: ' + error.message, 'error');
                }
            );
        }

        // Update UI based on tracking state
        function updateUI() {
            if (isTracking) {
                trackButton.textContent = 'Stop Tracking';
                trackButton.className = 'btn btn-danger btn-xl';
                statusIndicator.className = 'status-indicator status-active';
                statusText.textContent = 'Tracking Active';
                sessionInfo.classList.remove('hidden');
            } else {
                trackButton.textContent = 'Start Tracking';
                trackButton.className = 'btn btn-success btn-xl';
                statusIndicator.className = 'status-indicator status-inactive';
                statusText.textContent = 'Not Tracking';
            }
            
            // Update fullscreen tracking button if controls are visible
            if (!fullscreenControls.classList.contains('hidden')) {
                updateFullscreenTrackingButton();
            }
        }

        // Show status message
        function showMessage(message, type) {
            statusContent.textContent = message;
            statusContent.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-error');
            statusMessage.classList.remove('hidden');
            
            setTimeout(() => {
                statusMessage.classList.add('hidden');
            }, 5000);
        }
    </script>
    @endpush
</x-app-layout>