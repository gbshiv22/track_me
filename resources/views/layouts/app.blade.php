<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

        <!-- Page Content -->
        <main class="py-12">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Additional Scripts -->
    @stack('scripts')
    
    <!-- JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Fallback JavaScript -->
    <script>
        console.log('Track Me App - Fallback JS loaded');
        
        // Basic functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - Track Me app ready');
        });
    </script>
</body>
</html>
