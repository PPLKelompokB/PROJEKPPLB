@extends('layouts.app')

@section('title', 'Volunteer Dashboard - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-3xl font-medium text-gray-900 tracking-tight">
            Welcome back, {{ explode(' ', trim($user->name))[0] }}!
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Track your impact and manage your volunteer activities
        </p>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Events Joined -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">All Time</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalEvents) }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Events Joined</p>
        </div>

        <!-- Volunteer Points -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Level 5</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">2,450</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Volunteer Points</p>
        </div>

        <!-- Hours Volunteered -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">2025</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalHours) }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Hours Volunteered</p>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Next 30d</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ $upcomingEvents->count() }}</h3>
            <p class="text-xs font-medium text-gray-500 mt-1">Upcoming Events</p>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN --}}
        <div class="col-span-2">

            {{-- REGISTERED EVENTS --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Registered Events</h2>
                    <a href="#" class="text-[11px] font-semibold text-gray-500 hover:text-gray-900">View All</a>
                </div>

                <div class="space-y-4">
                    @forelse($upcomingEvents as $reg)
                        @if($reg->event)
                            <div class="border border-gray-200 rounded-xl p-5 flex flex-col sm:flex-row gap-5 items-start">
                                <!-- Icon -->
                                <div class="bg-gray-100 p-5 rounded-xl border border-gray-200 shrink-0">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 w-full">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $reg->event->title }}</h3>
                                        @if(strtolower($reg->event->status) == 'pending' || strtolower($reg->status) == 'pending')
                                            <span class="text-[10px] font-semibold text-gray-500">Pending</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-2 space-y-1.5">
                                        <p class="text-xs text-gray-600 flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            {{ \Carbon\Carbon::parse($reg->event->event_date)->format('l, F d, Y') }}
                                        </p>
                                        <p class="text-xs text-gray-600 flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            9:00 AM - 12:00 PM
                                        </p>
                                        <p class="text-xs text-gray-600 flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            {{ $reg->event->location }}
                                        </p>
                                    </div>

                                    <div class="mt-4 flex gap-2">
                                        <a href="/events/{{ $reg->event->id }}" class="bg-[#1a1c20] hover:bg-black text-white px-4 py-2 rounded-lg text-xs font-medium transition shadow-sm">
                                            View Details
                                        </a>
                                        <button class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-xs font-medium transition">
                                            Cancel RSVP
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="border border-gray-200 rounded-xl p-8 text-center bg-gray-50/50">
                            <p class="text-gray-500 text-sm font-medium">No registered events yet.</p>
                            <a href="/events" class="inline-block mt-3 text-sm text-black font-semibold hover:underline">Find an event</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- EVENT HISTORY --}}
            <div class="mt-6 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Event History</h2>
                    <a href="#" class="text-[11px] font-semibold text-gray-500 hover:text-gray-900">View All</a>
                </div>

                <div class="space-y-3">
                    @forelse($history as $item)
                        @if($item->event)
                            <div class="flex items-center justify-between border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="bg-gray-100 p-2.5 rounded-lg border border-gray-200">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-800">{{ $item->event->title }}</h3>
                                        <p class="text-[11px] text-gray-500 font-medium mt-0.5">
                                            {{ \Carbon\Carbon::parse($item->event->event_date)->format('F d, Y') }} &bull; 4 hours
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-[11px] font-semibold text-gray-600">+150 pts</span>
                                    <button class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-[10px] font-semibold transition">
                                        Certificate
                                    </button>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="border border-gray-200 rounded-xl p-6 text-center bg-gray-50/50">
                            <p class="text-gray-500 text-sm font-medium">No event history yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div>
            {{-- PROFILE SUMMARY --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900 mb-6">Profile Summary</h2>
                
                <div class="flex flex-col items-center">
                    <img 
                        src="{{ $user->photo ? asset($user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}"
                        class="w-20 h-20 rounded-full border border-gray-200 shadow-sm"
                        alt="{{ $user->name }}"
                    >

                    <h3 class="text-base font-semibold text-gray-900 mt-4">{{ $user->name }}</h3>
                    <p class="text-[11px] font-medium text-gray-500 mt-1">Ocean Guardian &bull; Level 5</p>
                    
                    <p class="text-xs text-gray-600 flex items-center gap-1.5 mt-2 font-medium">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Los Angeles, CA
                    </p>

                    <div class="w-full mt-6 space-y-4">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500 font-medium">Member Since</span>
                            <span class="text-gray-900 font-semibold">{{ $user->created_at->format('F Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500 font-medium">Total Impact</span>
                            <span class="text-gray-900 font-semibold">{{ $totalHours }} hours</span>
                        </div>
                    </div>

                    <button class="w-full mt-6 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2.5 rounded-lg text-xs font-semibold transition">
                        Edit Profile
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection