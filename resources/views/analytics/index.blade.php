@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
            <p class="mt-2 text-gray-600">Track your movement patterns, trip statistics, and location insights</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Trips</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $tripSummary['total_trips'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Distance</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($tripSummary['total_distance'] ?? 0, 1) }} km</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($tripSummary['average_speed'] ?? 0, 1) }} km/h</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dt class="text-sm font-medium text-gray-500 truncate">Carbon Footprint</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($tripSummary['total_carbon_footprint'] ?? 0, 2) }} kg CO₂</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('analytics.trip-statistics') }}" 
                       class="block w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition-colors duration-200">
                        📊 View Trip Statistics
                    </a>
                    <a href="{{ route('analytics.heat-map') }}" 
                       class="block w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-md transition-colors duration-200">
                        🗺️ View Heat Map
                    </a>
                    <a href="{{ route('analytics.reports') }}" 
                       class="block w-full text-left px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-md transition-colors duration-200">
                        📈 Generate Reports
                    </a>
                    <a href="{{ route('analytics.carbon-footprint') }}" 
                       class="block w-full text-left px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-md transition-colors duration-200">
                        🌱 Carbon Footprint Analysis
                    </a>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Insights</h3>
                @if(!empty($insights))
                    <div class="space-y-3">
                        @foreach($insights as $insight)
                            <div class="p-3 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-700">{{ $insight['message'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No insights available yet. Start tracking to see your patterns!</p>
                @endif
            </div>
        </div>

        <!-- Transport Mode Breakdown -->
        @if(!empty($tripSummary['transport_modes']))
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Transport Mode Breakdown</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($tripSummary['transport_modes'] as $mode => $count)
                    <div class="text-center p-3 bg-gray-50 rounded-md">
                        <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                        <div class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $mode) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No recent activity</h3>
                <p class="mt-1 text-sm text-gray-500">Start tracking your trips to see analytics here.</p>
                <div class="mt-6">
                    <a href="{{ route('tracking.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Start Tracking
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
