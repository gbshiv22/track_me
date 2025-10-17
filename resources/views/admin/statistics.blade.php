<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin - Statistics Dashboard') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.live-tracking') }}" class="text-blue-600 hover:text-blue-800">
                    Live Tracking
                </a>
                <a href="{{ route('admin.all-routes') }}" class="text-blue-600 hover:text-blue-800">
                    All Routes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Personal Analytics -->
            @if($tripSummary && $tripSummary->total_trips > 0)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Personal Analytics (Last 30 Days)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Trips -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Your Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $tripSummary->total_trips ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Distance -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Distance</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($tripSummary->total_distance ?? 0, 1) }} km</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Speed -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Avg Speed</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($tripSummary->average_speed ?? 0, 1) }} km/h</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carbon Footprint -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">CO₂ Footprint</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($tripSummary->total_carbon_footprint ?? 0, 2) }} kg</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Total Users</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</div>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Total Routes</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $totalSessions }}</div>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Active Now</div>
                            <div class="text-3xl font-bold text-gray-900">{{ $activeSessions }}</div>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Most Active</div>
                            <div class="text-lg font-bold text-gray-900">
                                {{ $mostActiveUser ? $mostActiveUser->name : 'N/A' }}
                            </div>
                            @if($mostActiveUser)
                                <div class="text-sm text-gray-600">
                                    {{ $mostActiveUser->tracking_sessions_count }} routes
                                </div>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions by Day Chart -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Routes Last 7 Days</h3>
                    <div class="h-64">
                        @if($sessionsByDay->count() > 0)
                            <div class="flex items-end justify-between h-full space-x-2">
                                @foreach($sessionsByDay as $day)
                                    @php
                                        $maxCount = $sessionsByDay->max('count');
                                        $height = $maxCount > 0 ? ($day->count / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="flex-1 flex flex-col items-center">
                                        <div class="w-full bg-blue-500 rounded-t hover:bg-blue-600 transition duration-200" 
                                             style="height: {{ $height }}%;"
                                             title="{{ $day->count }} routes">
                                        </div>
                                        <div class="text-xs text-gray-600 mt-2 text-center">
                                            {{ \Carbon\Carbon::parse($day->date)->format('M d') }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $day->count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center justify-center h-full text-gray-500">
                                No data available for the last 7 days
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Sessions -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Routes</h3>
                    @if($recentSessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentSessions as $session)
                                <div class="flex items-center justify-between border-b border-gray-200 pb-3 last:border-0">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $session->user->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            {{ $session->stopped_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-600">
                                            {{ $session->started_at->diffInMinutes($session->stopped_at) }} min
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $session->locationPoints->count() }} points
                                        </div>
                                    </div>
                                    <a href="{{ route('routes.show', $session->id) }}" 
                                       class="ml-4 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition duration-200">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            No recent routes
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>



