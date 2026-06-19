@extends('layouts.app')

@section('title', 'Manage Event Documentation - OceanCare')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900 mb-2">Manage Event Documentation</h1>
        <p class="text-gray-600">Review and manage documentation submitted by event organizers.</p>
    </div>

    @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col hover:shadow-md transition-shadow">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900 line-clamp-2 mb-1">{{ $event->title }}</h3>
                    <p class="text-sm text-gray-500">Organizer: {{ $event->organizer->name ?? 'Unknown' }}</p>
                </div>
                
                <div class="mb-6 flex-1">
                    <p class="text-sm text-gray-600 mb-2">
                        <span class="font-medium text-gray-800">Date:</span> {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium text-gray-800">Files Uploaded:</span> {{ $event->documentations->count() }}
                    </p>
                </div>

                <a href="{{ route('admin.documentation.show', $event->id) }}" class="block w-full text-center bg-black hover:bg-gray-800 text-white font-medium py-2.5 rounded-lg transition-colors text-sm">
                    Review Documentation
                </a>
            </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white border border-gray-200 rounded-xl shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Events Found</h3>
            <p class="text-gray-500 text-sm">There are currently no events with uploaded documentation to review.</p>
        </div>
    @endif
</div>
@endsection
