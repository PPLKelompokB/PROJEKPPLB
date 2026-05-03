@extends('layouts.app')

@section('title', 'Admin Dashboard - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-3xl font-medium text-gray-900 tracking-tight">
            Admin Dashboard
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Manage your beach clean-up events and track volunteer engagement
        </p>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 tracking-wide">+28%</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalUsers) }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Total Users</p>
        </div>

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
            <p class="text-xs font-medium text-gray-500 mt-1">Total Events</p>
        </div>

        <!-- Total Finished Events -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 tracking-wide">Live</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($finishedEvents) }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Total Finished Events</p>
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

                            {{-- STATUS --}}
                            <td class="py-4 px-6">
                                @if(strtolower($event->admin_status) == 'accepted' || strtolower($event->admin_status) == 'verified')
                                    <span class="bg-black text-white px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide capitalize">Accepted</span>
                                @elseif(strtolower($event->admin_status) == 'rejected')
                                    <span class="bg-gray-100 text-gray-500 border border-gray-200 px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide capitalize">Rejected</span>
                                @else
                                    <span class="bg-gray-50 text-gray-600 border border-gray-200 px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide capitalize">To Be Verified</span>
                                @endif
                            </td>

                            {{-- ACTION --}}
                            <td class="py-4 px-6 text-right space-x-2">
                                {{-- VIEW --}}
                                <a href="/events/{{ $event->id }}" class="inline-block p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="View Event">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                {{-- VERIFY / EDIT --}}
                                <a href="/admin/events/{{ $event->id }}/verify" class="inline-block p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded transition" title="Verify Event">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500 text-sm">
                                No events found.
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