<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- CSS -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <!-- Fallback for Vite (if available) -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            <!-- Navigation -->
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xl font-bold text-gray-900">Track Me</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
            @if (Route::has('login'))
                    @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @else
                                    <a href="{{ route('login') }}" class="nav-link">Log in</a>
                        @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                        @endif
                    @endauth
                            @endif
                        </div>
                    </div>
                </div>
                </nav>

            <!-- Hero Section -->
            <div class="bg-white">
                <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                            Track Your Journey
                        </h1>
                        <p class="mt-6 max-w-3xl mx-auto text-xl text-gray-500">
                            Record your location, track your routes, and discover patterns in your travels. 
                            Perfect for exploring new places and keeping track of your adventures.
                        </p>
                        <div class="mt-10 flex items-center justify-center space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                        Get Started
                                    </a>
                                    <a href="{{ route('login') }}" class="btn btn-outline btn-lg">
                                        Sign In
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="py-16 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900">Features</h2>
                        <p class="mt-4 text-lg text-gray-600">
                            Everything you need to track and analyze your location data
                        </p>
                    </div>
                    
                    <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="card text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Real-time Tracking</h3>
                            <p class="text-gray-600">
                                Track your location in real-time with configurable intervals. 
                                Perfect for recording your journey as it happens.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="card text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Route Visualization</h3>
                            <p class="text-gray-600">
                                View your routes on interactive maps with start and end points clearly marked. 
                                See exactly where you've been.
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="card text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Analytics & Stats</h3>
                            <p class="text-gray-600">
                                Get insights into your travel patterns, distances covered, 
                                and time spent in different locations.
                            </p>
                        </div>
                    </div>
                </div>
        </div>

            <!-- CTA Section -->
            <div class="bg-blue-600">
                <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-white">Ready to start tracking?</h2>
                        <p class="mt-4 text-xl text-blue-100">
                            Join thousands of users who are already tracking their journeys
                        </p>
                        <div class="mt-8">
        @if (Route::has('login'))
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn bg-white text-blue-600 hover:bg-gray-50 btn-lg">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="btn bg-white text-blue-600 hover:bg-gray-50 btn-lg">
                                        Get Started Free
                                    </a>
                                @endauth
        @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <div class="flex items-center justify-center space-x-2 mb-4">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-gray-900">Track Me</span>
                        </div>
                        <p class="text-gray-600">
                            © {{ date('Y') }} Track Me. Built with Laravel and modern web technologies.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>