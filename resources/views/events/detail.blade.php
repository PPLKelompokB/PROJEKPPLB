@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="font-medium text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column --}}
        <div class="lg:col-span-2">
            
            {{-- Hero Image --}}
            @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" class="w-full h-80 md:h-[400px] object-cover rounded-2xl mb-8">
            @else
                <div class="w-full h-80 md:h-[400px] bg-[#8c8c8c] rounded-2xl mb-8 flex items-center justify-center">
                    <span class="text-white text-xl font-medium">{{ $event->title }} Photo</span>
                </div>
            @endif

            {{-- Title Section --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-6">
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-gray-900 leading-tight">{{ $event->title }}</h1>
                    <div class="flex items-center gap-3 shrink-0">
                        @auth
                            @if(!$isRegistered && !$isFull)
                                <form action="{{ route('events.register', $event->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        Register as Volunteer
                                    </button>
                                </form>
                            @elseif($isRegistered)
                                <button disabled class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 cursor-default">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Registered
                                </button>
                            @elseif($isFull)
                                <button disabled class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-400 cursor-default">
                                    Event Full
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Login to Register
                            </a>
                        @endauth
                        <button class="p-2.5 border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-y-4 gap-x-8 text-sm text-gray-600">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->event_date)->addHours($event->duration)->format('g:i A') }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            {{-- Organized By --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-5">Organized by</h3>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ $event->organizer->name ?? 'Ocean Guardians Foundation' }}</h4>
                        <p class="text-xs text-gray-500 mt-1">Environmental Non-Profit • 250+ events organized</p>
                        <div class="flex items-center gap-1 mt-1.5">
                            <div class="flex text-gray-900">
                                @for($i=0; $i<5; $i++)
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500 ml-1">4.9 (127 reviews)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- About This Event --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-5">About This Event</h3>
                <div class="text-sm text-gray-600 space-y-4 leading-relaxed">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            {{-- Event Location --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-5">Event Location</h3>
                <div class="w-full h-56 bg-[#d1d5db] rounded-xl mb-5 flex flex-col items-center justify-center text-gray-600">
                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    <span class="text-sm font-medium text-gray-800">Interactive Map</span>
                    <span class="text-xs mt-1">{{ $event->location }}</span>
                </div>
                
                @if($event->meeting_point)
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Meeting Point</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $event->meeting_point }}</p>
                    </div>
                </div>
                @endif

                @if($event->contact_person || $event->contact_phone)
                <div class="flex items-start mt-4 pt-4 border-t border-gray-100">
                    <svg class="w-5 h-5 mr-3 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Contact Person</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $event->contact_person ?? 'Event Coordinator' }} @if($event->contact_phone) &bull; {{ $event->contact_phone }} @endif</p>
                    </div>
                </div>
                @endif
            </div>

        </div>

        {{-- Right Column (Sidebar) --}}
        <div class="lg:col-span-1">
            
            {{-- Registration Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6 shadow-sm">
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-semibold text-gray-900">Free</h2>
                    <p class="text-sm text-gray-500 mt-1">Community Event</p>
                </div>

                @php
                    $percentage = $event->quota > 0 ? round(($totalVolunteers / $event->quota) * 100) : 0;
                    $remaining = $event->quota - $totalVolunteers;
                @endphp

                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Volunteers Registered</span>
                        <span class="font-semibold text-gray-900">{{ $totalVolunteers }} / {{ $event->quota }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2 overflow-hidden">
                        <div class="bg-gray-800 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ max(0, $remaining) }} spots remaining</span>
                        <span>{{ $percentage }}% full</span>
                    </div>
                </div>

                @auth
                    @if(!$isRegistered && !$isFull)
                        <form action="{{ route('events.register', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors mb-4">
                                Register Now
                            </button>
                        </form>
                    @elseif($isRegistered)
                        <button disabled class="w-full py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 cursor-default mb-4 flex justify-center items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Registered
                        </button>
                    @elseif($isFull)
                        <button disabled class="w-full py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gray-400 cursor-default mb-4">
                            Event Full
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 transition-colors mb-4">
                        Login to Register
                    </a>
                @endauth

                <div class="text-center">
                    <a href="#" class="text-xs font-medium text-gray-500 hover:text-gray-800 underline decoration-gray-300 underline-offset-4 transition-colors">Add to Calendar</a>
                </div>
            </div>

            {{-- Event Details Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-5">Event Details</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <div>
                            <h4 class="text-xs font-medium text-gray-900">Date</h4>
                            <p class="text-sm text-gray-600 mt-0.5">{{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h4 class="text-xs font-medium text-gray-900">Time</h4>
                            <p class="text-sm text-gray-600 mt-0.5">{{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->event_date)->addHours($event->duration)->format('g:i A') }} {{ \Carbon\Carbon::parse($event->event_date)->format('T') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <div>
                            <h4 class="text-xs font-medium text-gray-900">Capacity</h4>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $event->quota }} volunteers maximum</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h4 class="text-xs font-medium text-gray-900">Impact Goal</h4>
                            <p class="text-sm text-gray-600 mt-0.5">Community Environment</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Volunteers Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-5">Recent Volunteers</h3>
                
                <div class="space-y-4 mb-4">
                    @forelse($event->registrations->take(3) as $registration)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center shrink-0">
                            @if($registration->user && $registration->user->avatar)
                                <img src="{{ asset('storage/' . $registration->user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $registration->user->name ?? 'Anonymous' }}</p>
                            <p class="text-xs text-gray-500">Registered {{ $registration->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-2">No volunteers registered yet.</p>
                    @endforelse
                </div>

                @if($event->registrations->count() > 3)
                <div class="text-center mt-5 pt-4 border-t border-gray-100">
                    <a href="#" class="text-xs font-medium text-gray-500 hover:text-gray-800 underline decoration-gray-300 underline-offset-4 transition-colors">View all volunteers</a>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection