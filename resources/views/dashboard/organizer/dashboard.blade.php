@extends('layouts.app')

@section('title', 'Organizer Dashboard - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-medium text-gray-900 tracking-tight">
                Organizer Dashboard
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Manage your beach clean-up events and volunteer engagement
            </p>
        </div>

        <a href="/events/create" 
           class="bg-[#1a1c20] hover:bg-black text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Event
        </a>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Events -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 tracking-wide">+12%</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalEvents) }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Total Events Created</p>
        </div>

        <!-- Total Volunteers -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 tracking-wide">+28%</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalVolunteers) }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Total Volunteers Participated</p>
        </div>

        <!-- Active Events -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 tracking-wide">Live</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($activeEvents) }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Active Events</p>
        </div>
    </div>

    {{-- EVENT MANAGEMENT --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <!-- Header -->
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Event Management</h2>
                <p class="text-xs text-gray-500 mt-1 font-medium">View and manage all your beach clean-up events</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Search events..."
                        class="border border-gray-200 rounded-lg pl-9 pr-4 py-2.5 text-sm w-64 focus:outline-none focus:ring-1 focus:ring-gray-300 transition">
                </div>
                <button class="border border-gray-200 rounded-lg p-2.5 text-gray-600 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#fafafa] border-b border-gray-100">
                    <tr>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6">Event Name</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6">Date</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6">Location</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6">Volunteers</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6">Status</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            
                            {{-- EVENT --}}
                            <td class="py-4 px-6 flex items-center gap-4">
                                <div class="bg-gray-100 p-2.5 rounded-lg border border-gray-200">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $event->title }}</p>
                                    <p class="text-[11px] font-medium text-gray-500 mt-0.5 whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px]">
                                        {{ Str::limit($event->description, 40) }}
                                    </p>
                                </div>
                            </td>

                            {{-- DATE --}}
                            <td class="py-4 px-6 text-xs text-gray-600 font-medium whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                            </td>

                            {{-- LOCATION --}}
                            <td class="py-4 px-6 text-xs text-gray-600 font-medium">
                                {{ $event->location }}
                            </td>

                            {{-- VOLUNTEERS --}}
                            <td class="py-4 px-6 text-xs font-semibold text-gray-800">
                                {{ $event->registrations ? $event->registrations->count() : 0 }} <span class="text-gray-400 font-medium">/ {{ $event->quota }}</span>
                            </td>

                            {{-- STATUS --}}
                            <td class="py-4 px-6">
                                @if(strtolower($event->status) == 'upcoming')
                                    <span class="bg-gray-100 text-gray-600 border border-gray-200 px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide">Upcoming</span>
                                @elseif(strtolower($event->status) == 'completed')
                                    <span class="bg-black text-white px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide">Completed</span>
                                @else
                                    <span class="bg-white border border-gray-200 text-gray-600 px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide capitalize">{{ $event->status ?? 'Draft' }}</span>
                                @endif
                            </td>

                            {{-- ACTION --}}
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="/events/{{ $event->id }}/edit" class="inline-block p-1.5 text-gray-400 hover:text-gray-800 hover:bg-gray-100 rounded transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>

                                <form action="/events/{{ $event->id }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500 text-sm">
                                No events found. Create your first event to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 flex justify-between items-center bg-white">
            <p class="text-[11px] font-medium text-gray-500">
                Showing 1 to {{ count($events) }} of {{ $totalEvents }} events
            </p>
            <div class="flex items-center gap-1.5">
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-500 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center bg-black text-white rounded font-medium text-xs shadow-sm">
                    1
                </button>
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition font-medium text-xs">
                    2
                </button>
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition font-medium text-xs">
                    3
                </button>
                <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-500 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

    </div>

</div>
@endsection