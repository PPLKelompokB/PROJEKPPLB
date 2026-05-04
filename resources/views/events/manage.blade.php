@extends('layouts.app')

@section('title', 'Manage Your Events - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-10">
        <h1 class="text-3xl font-medium text-gray-900 tracking-tight">
            Manage Your Events
        </h1>
        <p class="text-sm text-gray-600 mt-2">
            Easily view, edit, and organize all your events in one place. Stay in control and keep everything up to date.
        </p>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col hover:shadow-md transition">
                <!-- Image Placeholder -->
                <div class="bg-gray-300 h-48 w-full flex items-center justify-center text-white text-sm font-medium">
                    Event Image
                </div>

                <!-- Content -->
                <div class="p-6 flex flex-col flex-grow">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $event->title }}</h2>

                    <div class="space-y-2 mb-6 flex-grow">
                        <!-- Location -->
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600">{{ $event->location }}</span>
                        </div>

                        <!-- Date -->
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600">
                                {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
                            </span>
                        </div>

                        <!-- Participants -->
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600">
                                {{ $event->registrations_count ?? 0 }}/{{ $event->quota }} volunteers
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2 mt-auto">
                        <a href="/events/{{ $event->id }}" class="w-full text-center bg-black hover:bg-gray-800 text-white py-2.5 rounded-lg text-xs font-semibold transition">
                            View Details
                        </a>
                        <a href="/events/{{ $event->id }}/participants" class="w-full text-center bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 py-2.5 rounded-lg text-xs font-semibold transition">
                            View Participants
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-gray-500">
                <p class="text-sm font-medium">No events found.</p>
                <a href="/events/create" class="inline-block mt-4 bg-black text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition">
                    + Create New Event
                </a>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($events->hasPages())
    <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-transparent">
        <p class="text-[11px] font-medium text-gray-500">
            Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events
        </p>

        <div class="flex items-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($events->onFirstPage())
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-400 cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
            @else
                <a href="{{ $events->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-500 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                @if ($page == $events->currentPage())
                    <button class="w-8 h-8 flex items-center justify-center bg-black text-white rounded font-medium text-xs shadow-sm">
                        {{ $page }}
                    </button>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition font-medium text-xs">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($events->hasMorePages())
                <a href="{{ $events->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-500 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-400 cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection