<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">
            My Routes
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if(session('success'))
            <div class="alert alert-success mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            
            @if($sessions->count() > 0)
                <div class="space-y-4">
                    @foreach($sessions as $session)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                        Route - {{ $session->started_at->format('M d, Y') }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                        <div>
                                            <span class="font-medium">Started:</span> {{ $session->started_at->format('h:i A') }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Stopped:</span> {{ $session->stopped_at ? $session->stopped_at->format('h:i A') : 'N/A' }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Duration:</span> 
                                            @if($session->stopped_at)
                                                {{ $session->started_at->diffInMinutes($session->stopped_at) }} min
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium">Points:</span> {{ $session->locationPoints->count() }}
                                    </div>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <a href="{{ route('routes.show', $session->id) }}" 
                                       class="btn btn-primary btn-sm">
                                        View Route
                                    </a>
                                    <form method="POST" action="{{ route('routes.destroy', $session->id) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this route?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No routes yet</h3>
                    <p class="text-sm text-gray-500 mb-6">Start tracking your location to create routes</p>
                    <a href="{{ route('tracking.index') }}" class="btn btn-primary">
                        Start Tracking
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>