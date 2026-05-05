@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="p-8 max-w-6xl mx-auto bg-gray-50 min-h-screen">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-2">
            
            {{-- IMAGE --}}
            @if($event->image)
                <img src="{{ asset('storage/'.$event->image) }}" class="w-full h-[400px] object-cover rounded-xl mb-6 shadow-sm">
            @else
                <div class="w-full h-[400px] bg-gray-400 rounded-xl flex items-center justify-center text-white text-lg font-medium mb-6 shadow-sm">
                    Beach Clean-Up Event Photo
                </div>
            @endif

            {{-- TITLE SECTION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="mb-2">
                            @if($event->completion_status === 'Upcoming')
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Upcoming</span>
                            @elseif($event->completion_status === 'Ongoing')
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Ongoing</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Finished</span>
                            @endif
                        </div>
                        <h1 class="text-[28px] font-semibold text-gray-900 leading-tight">{{ $event->title }}</h1>
                    </div>
                    
                    <div class="flex gap-2 shrink-0">
                        @if(!auth()->check() || auth()->user()->role === 'volunteer')
                        <button class="bg-black text-white px-4 py-2 rounded-md text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Register as Volunteer
                        </button>
                        @endif
                        <button class="border border-gray-300 p-2 rounded-md hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->event_date)->addHours($event->duration)->format('g:i A') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            {{-- ORGANIZED BY --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-base font-medium text-gray-800 mb-4">Organized by</h2>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
                        {{-- Placeholder avatar --}}
                        <svg class="w-full h-full text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $event->organizer->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Environmental Non-Profit • 250+ events organized</p>
                        <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                            <span class="text-gray-400 text-[10px]">⭐⭐⭐⭐⭐</span> 4.9 (127 reviews)
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABOUT EVENT --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-base font-medium text-gray-800 mb-4">About This Event</h2>
                <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $event->description }}</div>
                
                @if(strpos(strtolower($event->description), 'what to bring') === false)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-800 mb-2">What to Bring:</p>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Comfortable walking shoes</li>
                        <li>Sun protection (hat, sunscreen)</li>
                        <li>Water bottle</li>
                        <li>Positive attitude!</li>
                    </ul>
                </div>
                @endif
            </div>

            {{-- EVENT LOCATION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-base font-medium text-gray-800 mb-4">Event Location</h2>
                
                <div class="w-full h-64 bg-gray-200 rounded-lg mb-4 overflow-hidden">
                    <iframe 
                        src="https://www.google.com/maps?q={{ urlencode($event->location) }}&output=embed"
                        width="100%" height="100%"
                        style="border:0;"
                        allowfullscreen="" loading="lazy">
                    </iframe>
                </div>

                @if($event->meeting_point)
                <div class="flex items-start gap-3 bg-gray-50 p-4 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Meeting Point</p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $event->meeting_point }}<br>{{ $event->location }}</p>
                    </div>
                </div>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="lg:col-span-1">
            
            {{-- REGISTRATION CARD --}}
            @if((!auth()->check() || auth()->user()->role === 'volunteer') && !\Carbon\Carbon::parse($event->event_date)->isPast())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Free</h2>
                    <p class="text-xs text-gray-500 mt-1">Community Event</p>
                </div>

                @php
                    $percent = ($event->quota > 0) ? ($totalVolunteers / $event->quota) * 100 : 0;
                    $remaining = max(0, $event->quota - $totalVolunteers);
                @endphp

                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Volunteers Registered</span>
                        <span class="font-medium text-gray-900">{{ $totalVolunteers }} / {{ $event->quota }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-gray-800 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ $remaining }} spots remaining</span>
                        <span>{{ round($percent) }}% full</span>
                    </div>
                </div>

                {{-- BUTTON LOGIC --}}
                @auth
                    @if($isRegistered)
                        <button class="w-full bg-green-600 text-white py-2.5 rounded-md text-sm font-medium cursor-default">
                            ✔ Already Registered
                        </button>
                    @elseif($isFull)
                        <button class="w-full bg-red-600 text-white py-2.5 rounded-md text-sm font-medium cursor-default">
                            Event Full
                        </button>
                    @else
                        <form id="registerForm" action="{{ route('events.register', $event->id) }}" method="POST">
                            @csrf
                            <button type="button" onclick="openConfirmModal()" class="w-full bg-black text-white py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition">
                                Register Now
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center bg-black text-white py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition">
                        Login to Register
                    </a>
                @endauth

            </div>
            @endif

            {{-- EVENT DETAILS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-base font-medium text-gray-800 mb-4">Event Details</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Date</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Time</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->event_date)->addHours($event->duration)->format('g:i A') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Capacity</p>
                            <p class="text-xs text-gray-500">{{ $event->quota }} volunteers maximum</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Impact Goal</p>
                            <p class="text-xs text-gray-500">Remove 500+ lbs of waste</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RECENT VOLUNTEERS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-base font-medium text-gray-800 mb-4">Recent Volunteers</h3>
                
                <div class="space-y-4">
                    @forelse($event->registrations->take(5) as $reg)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
                                <svg class="w-full h-full text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $reg->user->name }}</p>
                                <p class="text-xs text-gray-500">Registered {{ $reg->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada volunteer</p>
                    @endforelse
                </div>

                @if($event->registrations->count() > 5)
                    <div class="text-center mt-4">
                        <a href="{{ route('events.participants', $event->id) }}" class="text-xs text-gray-500 underline hover:text-gray-800 transition">View all volunteers</a>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>

{{-- MODALS --}}
<!-- Registration Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-xl transform transition-all">
        <h3 class="text-lg font-semibold text-gray-900 mb-6 text-center">Are you sure you want to register this event?</h3>
        <div class="flex gap-4">
            <button type="button" onclick="closeConfirmModal()" class="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </button>
            <button type="button" onclick="submitRegistration()" class="flex-1 py-2.5 bg-black rounded-lg text-sm font-medium text-white hover:bg-gray-800 transition">
                Register!
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
@if(session('success') && session('success') == 'Berhasil mendaftar!')
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-xl transform transition-all text-center">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">You have successfully registered for this event.</h3>
        <button type="button" onclick="closeSuccessModal()" class="w-full py-2.5 bg-black rounded-lg text-sm font-medium text-white hover:bg-gray-800 transition">
            Thanks!
        </button>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openConfirmModal() {
        document.getElementById('confirmModal').classList.remove('hidden');
        document.getElementById('confirmModal').classList.add('flex');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        document.getElementById('confirmModal').classList.remove('flex');
    }

    function submitRegistration() {
        document.getElementById('registerForm').submit();
    }

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
    }
</script>
@endpush

@endsection