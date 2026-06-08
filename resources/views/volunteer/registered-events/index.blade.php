@extends('layouts.app')

@section('title', 'Registered Events')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Registered Events</h1>
            <p class="text-gray-500 text-sm">View and manage the events you have registered for.</p>
        </div>

        {{-- Filter Dropdowns --}}
        <form id="filter-form" action="{{ route('volunteer.registered-events') }}" method="GET" class="flex gap-3 flex-wrap">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search event..."
                    class="appearance-none border border-gray-300 rounded-lg py-2 pl-3 pr-8 text-sm text-gray-700 bg-white focus:outline-none focus:border-gray-500">
            </div>
            
            <div class="relative">
                <select name="status" onchange="this.form.submit()"
                    class="appearance-none border border-gray-300 rounded-lg py-2 pl-3 pr-8 text-sm text-gray-700 bg-white focus:outline-none focus:border-gray-500 cursor-pointer">
                    <option value="">Event Status</option>
                    <option value="upcoming"   {{ request('status') == 'upcoming'   ? 'selected' : '' }}>Upcoming</option>
                    <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Registered</option>
                    <option value="completed"  {{ request('status') == 'completed'  ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled"  {{ request('status') == 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            <div class="relative">
                <select name="date" onchange="this.form.submit()"
                    class="appearance-none border border-gray-300 rounded-lg py-2 pl-3 pr-8 text-sm text-gray-700 bg-white focus:outline-none focus:border-gray-500 cursor-pointer">
                    <option value="">Event Date</option>
                    <option value="asc"  {{ request('date') == 'asc'  ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('date') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    {{-- Empty State --}}
    @if($registrations->isEmpty())
        <div class="text-center py-16 bg-white border border-gray-200 rounded-xl">
            <svg class="mx-auto h-14 w-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                       M9 5a2 2 0 002 2h2a2 2 0 002-2
                       M9 5a2 2 0 012-2h2a2 2 0 012 2
                       m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            @if(request()->filled('search') || request()->filled('status'))
                <p class="text-sm text-gray-500">Tidak ada event yang sesuai dengan filter yang dipilih.</p>
            @else
                <p class="text-sm text-gray-500">Belum ada event yang terdaftar.</p>
            @endif
        </div>

    @else
        {{-- Events Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($registrations as $reg)
                @php
                    $event      = $reg->event;
                    $isPast     = \Carbon\Carbon::parse($event->event_date)->isPast();

                    if ($reg->status === 'cancelled') {
                        $statusText  = 'Cancelled';
                        $statusClass = 'bg-white/90 text-gray-700';
                    } elseif ($isPast) {
                        $statusText  = 'Completed';
                        $statusClass = 'bg-white/90 text-gray-700';
                    } elseif ($reg->status === 'registered') {
                        $statusText  = 'Registered';
                        $statusClass = 'bg-white/90 text-gray-700';
                    } else {
                        $statusText  = 'Upcoming';
                        $statusClass = 'bg-white/90 text-gray-700';
                    }
                @endphp

                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col">

                    {{-- Image with overlaid status + date --}}
                    <div class="relative w-full h-48 bg-gray-200">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}"
                                 alt="{{ $event->title }}"
                                 class="w-full h-full object-cover">
                        @endif

                        {{-- Overlay: status (left) + date (right) at bottom of image --}}
                        <div class="absolute bottom-0 left-0 right-0 flex items-center justify-between px-3 py-2 bg-gradient-to-t from-black/10 to-transparent">
                            <span class="text-xs font-medium px-2 py-0.5 rounded {{ $statusClass }} shadow-sm">
                                {{ $statusText }}
                            </span>
                            <span class="text-xs font-medium text-white bg-black/40 px-2 py-0.5 rounded">
                                {{ \Carbon\Carbon::parse($event->event_date)->format('M j, Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Card body --}}
                    <div class="p-4 flex-1 flex flex-col">
                        {{-- Title --}}
                        <h3 class="text-[15px] font-bold text-gray-900 mb-3 line-clamp-2 leading-snug">
                            {{ $event->title }}
                        </h3>

                        {{-- Location row --}}
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0
                                       l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">{{ $event->location ?? '-' }}</span>
                        </div>

                        {{-- Organizer row --}}
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-4">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="truncate">{{ optional($event->organizer)->name ?? 'OceanCare' }}</span>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex gap-2 mt-auto">
                            @if($statusText === 'Cancelled')
                                {{-- Cancelled: full-width disabled button --}}
                                <button disabled
                                    class="w-full py-2 px-4 rounded-lg text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed text-center">
                                    Event Cancelled
                                </button>

                            @elseif($statusText === 'Completed')
                                {{-- Completed: View Details + Certificate --}}
                                <a href="{{ route('volunteer.registered-events.show', $event->id) }}"
                                   class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold text-white bg-black hover:bg-gray-800 transition-colors text-center">
                                    View Details
                                </a>
                                <a href="#"
                                   class="py-2 px-3 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 flex items-center gap-1.5 transition-colors whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Certificate
                                </a>

                            @else
                                {{-- Upcoming / Registered: View Details + Cancel --}}
                                <a href="{{ route('volunteer.registered-events.show', $event->id) }}"
                                   class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold text-white bg-black hover:bg-gray-800 transition-colors text-center">
                                    View Details
                                </a>
                                <button
                                    class="py-2 px-3 border border-gray-300 rounded-lg text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach

        </div>

        {{-- Pagination / Load More --}}
        <div class="mt-10 flex justify-center">
            @if($registrations->hasPages())
                {{ $registrations->links() }}
            @else
                <button class="px-8 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                    Load More Events
                </button>
            @endif
        </div>

    @endif
</div>
@endsection
