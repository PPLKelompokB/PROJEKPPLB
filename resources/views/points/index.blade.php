@extends('layouts.app')

@section('title', 'Volunteer Points')

@section('content')
<div class="px-10 py-8 max-w-7xl mx-auto">

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Volunteer Points</h1>
        <p class="text-gray-500 mt-2 text-sm">Track the points you earn from participating in beach clean-up events.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1: Total Points Earned -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">{{ number_format($totalPoints) }}</h2>
                <p class="text-xs text-gray-500 font-medium mt-1 uppercase tracking-wide">Total Points Earned</p>
            </div>
        </div>

        <!-- Card 2: Total Events Joined -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">{{ number_format($totalEvents) }}</h2>
                <p class="text-xs text-gray-500 font-medium mt-1 uppercase tracking-wide">Total Events Joined</p>
            </div>
        </div>

        <!-- Card 3: Volunteer Rank -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">#{{ $rank }}</h2>
                <p class="text-xs text-gray-500 font-medium mt-1 uppercase tracking-wide">Volunteer Rank</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Points History -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-white">
                    <h2 class="text-lg font-bold text-gray-900">Points History</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Event Name</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Event Date</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Points Earned</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($points as $point)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-center font-medium text-gray-800">
                                        {{ $point->event->title ?? 'Unknown Event' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ $point->event ? \Carbon\Carbon::parse($point->event->event_date)->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-900">
                                        +{{ $point->points }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                        No points history available yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Top Volunteers -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 bg-white">
                    <h2 class="text-lg font-bold text-gray-900">Top Volunteers</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-6">
                        @foreach($leaderboard as $index => $leader)
                            @php
                                $isCurrentUser = $leader->id === auth()->id();
                            @endphp
                            <div class="flex items-center {{ $isCurrentUser ? 'bg-gray-50 rounded-lg p-3 -mx-3 border border-gray-100' : '' }}">
                                <!-- Rank Number -->
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm
                                    {{ $index === 0 ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $index + 1 }}
                                </div>
                                
                                <!-- Avatar -->
                                <div class="ml-4 flex-shrink-0">
                                    <img src="{{ $leader->photo ? asset($leader->photo) : 'https://ui-avatars.com/api/?name='.urlencode($leader->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $leader->name }}" class="w-10 h-10 rounded-sm object-cover border border-gray-200">
                                </div>
                                
                                <!-- Name & Points -->
                                <div class="ml-4 flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $isCurrentUser ? 'You' : $leader->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ number_format($leader->points ?? 0) }} points
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection