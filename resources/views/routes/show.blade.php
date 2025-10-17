<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Route Details') }} - {{ $session->started_at->format('M d, Y h:i A') }}
            </h2>
            <a href="{{ route('routes.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Routes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600 mb-1">Duration</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $duration }} min</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600 mb-1">Distance</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalDistance }} km</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600 mb-1">Points Recorded</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $session->locationPoints->count() }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600 mb-1">Avg Speed</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $duration > 0 ? round(($totalDistance / $duration) * 60, 1) : 0 }} km/h
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Route Map</h3>
                    <div id="map" style="height: 500px; width: 100%;" class="rounded-lg border border-gray-300"></div>
                </div>
            </div>

            <!-- Open in Google Maps -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">External Navigation</h3>
                    <a href="https://www.google.com/maps/dir/?api=1&origin={{ $session->start_latitude }},{{ $session->start_longitude }}&destination={{ $session->end_latitude }},{{ $session->end_longitude }}" 
                       target="_blank"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        Open in Google Maps
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

    @push('scripts')
    <script>
        // Initialize map
        const locationPoints = @json($session->locationPoints->map(function($point) {
            return [
                'lat' => $point->latitude,
                'lng' => $point->longitude,
                'time' => $point->recorded_at->format('H:i:s')
            ];
        }));

        if (locationPoints.length > 0) {
            // Create map centered on first point
            const map = L.map('map').setView([locationPoints[0].lat, locationPoints[0].lng], 14);

            // Add OpenStreetMap tiles (FREE, no API key needed!)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Create polyline from all points
            const latLngs = locationPoints.map(point => [point.lat, point.lng]);
            const polyline = L.polyline(latLngs, {
                color: 'blue',
                weight: 4,
                opacity: 0.7
            }).addTo(map);

            // Add start marker (green)
            const startMarker = L.marker([locationPoints[0].lat, locationPoints[0].lng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: #10b981; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                })
            }).addTo(map);
            startMarker.bindPopup(`<b>Start Point</b><br>Time: ${locationPoints[0].time}`);

            // Add end marker (red)
            const lastPoint = locationPoints[locationPoints.length - 1];
            const endMarker = L.marker([lastPoint.lat, lastPoint.lng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: #ef4444; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                })
            }).addTo(map);
            endMarker.bindPopup(`<b>End Point</b><br>Time: ${lastPoint.time}`);

            // Fit map to show entire route
            map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
        }
    </script>
    @endpush
</x-app-layout>



