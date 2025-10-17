<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin - Live Tracking Dashboard') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.all-routes') }}" class="text-blue-600 hover:text-blue-800">
                    All Routes
                </a>
                <a href="{{ route('admin.statistics') }}" class="text-blue-600 hover:text-blue-800">
                    Statistics
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Active Users Summary -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Active Tracking Sessions</h3>
                        <p class="text-sm text-gray-600">Currently tracking users in real-time</p>
                    </div>
                    <div class="text-4xl font-bold text-blue-600" id="active-count">{{ $activeSessions->count() }}</div>
                </div>
            </div>

            <!-- Map showing all active users -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Live Map</h3>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-sm text-gray-600">Auto-refreshing every 10s</span>
                        </div>
                    </div>
                    <div id="map" style="height: 500px; width: 100%;" class="rounded-lg border border-gray-300"></div>
                </div>
            </div>

            <!-- Active Users List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Active Users</h3>
                    <div id="users-list">
                        @if($activeSessions->count() > 0)
                            <div class="space-y-3">
                                @foreach($activeSessions as $session)
                                    <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $session->user->name }}</div>
                                            <div class="text-sm text-gray-600">
                                                Started: {{ $session->started_at->diffForHumans() }}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                Points: {{ $session->locationPoints->count() }}
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                            <span class="text-sm text-green-600 font-medium">Active</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                No users are currently tracking their location
                            </div>
                        @endif
                    </div>
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
        let map;
        let markers = {};

        // Initialize map
        function initMap() {
            if (!map) {
                // Default center (will adjust based on data)
                map = L.map('map').setView([20, 0], 2);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);
            }
        }

        // Update active sessions on map
        function updateActiveSessions() {
            fetch('{{ route("admin.active-sessions") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateMap(data.sessions);
                    updateUsersList(data.sessions);
                    document.getElementById('active-count').textContent = data.sessions.length;
                }
            })
            .catch(error => console.error('Error fetching active sessions:', error));
        }

        // Update map markers
        function updateMap(sessions) {
            if (!map) initMap();

            // Remove old markers
            Object.values(markers).forEach(marker => map.removeLayer(marker));
            markers = {};

            if (sessions.length === 0) return;

            // Add new markers
            const bounds = [];
            sessions.forEach(session => {
                const lat = parseFloat(session.latitude);
                const lng = parseFloat(session.longitude);
                
                if (lat && lng) {
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="background-color: #3b82f6; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-center; color: white; font-weight: bold; font-size: 12px;">${session.user_name.charAt(0)}</div>`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        })
                    }).addTo(map);

                    marker.bindPopup(`
                        <b>${session.user_name}</b><br>
                        Started: ${session.started_at}<br>
                        Last Update: ${session.recorded_at || 'N/A'}
                    `);

                    markers[session.id] = marker;
                    bounds.push([lat, lng]);
                }
            });

            // Fit map to show all markers
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        // Update users list
        function updateUsersList(sessions) {
            const usersList = document.getElementById('users-list');
            
            if (sessions.length === 0) {
                usersList.innerHTML = '<div class="text-center py-8 text-gray-500">No users are currently tracking their location</div>';
                return;
            }

            usersList.innerHTML = '<div class="space-y-3">' + sessions.map(session => `
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                    <div>
                        <div class="font-medium text-gray-900">${session.user_name}</div>
                        <div class="text-sm text-gray-600">Started: ${session.started_at}</div>
                        <div class="text-sm text-gray-600">Last Update: ${session.recorded_at || 'N/A'}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-sm text-green-600 font-medium">Active</span>
                    </div>
                </div>
            `).join('') + '</div>';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            updateActiveSessions();

            // Auto-refresh every 10 seconds
            setInterval(updateActiveSessions, 10000);
        });
    </script>
    @endpush
</x-app-layout>



