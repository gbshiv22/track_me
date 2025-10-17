<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin - All Users Routes') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.live-tracking') }}" class="text-blue-600 hover:text-blue-800">
                    Live Tracking
                </a>
                <a href="{{ route('admin.statistics') }}" class="text-blue-600 hover:text-blue-800">
                    Statistics
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($sessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($sessions as $session)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg font-medium text-gray-900">
                                                    {{ $session->user->name }}
                                                </h3>
                                                <span class="text-sm text-gray-500">
                                                    {{ $session->started_at->format('M d, Y h:i A') }}
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                                <div>
                                                    <strong>Duration:</strong> 
                                                    @if($session->stopped_at)
                                                        {{ $session->started_at->diffInMinutes($session->stopped_at) }} min
                                                    @else
                                                        N/A
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>Points:</strong> {{ $session->locationPoints->count() }}
                                                </div>
                                                <div>
                                                    <strong>User Email:</strong> {{ $session->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('routes.show', $session->id) }}" 
                                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                View Route
                                            </a>
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
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No routes yet</h3>
                            <p class="mt-1 text-sm text-gray-500">No users have completed any tracking sessions</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>



