@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900 mb-2">Beach Clean-Up Events</h1>
        <p class="text-gray-600">Join our community in protecting marine ecosystems through our beach clean-up events.</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-8">
        <form id="filter-form" action="{{ route('events.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Events</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-black focus:border-black sm:text-sm" placeholder="Search by event name or beach...">
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" value="{{ request('location') }}" 
                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-black focus:border-black sm:text-sm" 
                        placeholder="Type a city or area...">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <select name="date" class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 focus:outline-none focus:ring-1 focus:ring-black focus:border-black sm:text-sm rounded-lg appearance-none bg-white bg-no-repeat" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-position: right 0.7rem top 50%; background-size: 0.65rem auto;">
                        <option value="">All Dates</option>
                        <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ request('date') == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('date') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-4">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search Events
                </button>
                <a href="{{ route('events.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Results Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="text-sm text-gray-500">
            Showing {{ $events->total() }} events
        </div>
        <div class="flex items-center gap-3">
            <label class="text-sm text-gray-500">Sort by:</label>
            <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit()" class="block pl-3 pr-8 py-1.5 text-sm border border-gray-300 focus:outline-none focus:ring-1 focus:ring-black focus:border-black rounded-lg appearance-none bg-white bg-no-repeat font-medium" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-position: right 0.5rem top 50%; background-size: 0.65rem auto;">
                <option value="earliest" {{ request('sort') == 'earliest' ? 'selected' : '' }}>Date (Earliest)</option>
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Date (Latest)</option>
            </select>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        @foreach($events as $event)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col hover:shadow-md transition-shadow duration-200">
            @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" class="w-full h-52 object-cover">
            @else
                <div class="w-full h-52 bg-[#d1d5db] flex items-center justify-center">
                    <span class="text-white font-medium text-lg">{{ $event->location ?? 'Beach Area' }}</span>
                </div>
            @endif
            
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 line-clamp-2">{{ $event->title }}</h3>
                
                <div class="space-y-3 mb-6 flex-1">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-4 w-4 mr-2.5 text-gray-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate">{{ $event->location }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-4 w-4 mr-2.5 text-gray-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y • g:i A') }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-4 w-4 mr-2.5 text-gray-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ $event->registrations_count ?? 0 }}/{{ $event->quota }} volunteers</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-4 w-4 mr-2.5 text-gray-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="truncate">{{ $event->organizer->name ?? 'Ocean Guardians' }}</span>
                    </div>
                </div>
                
                <a href="{{ route('events.show', $event->id) }}" class="block w-full py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 text-center transition-colors">
                    View Details
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $events->links() }}
    </div>
</div>
@endsection