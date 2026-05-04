@props([
    'event',
    'mode' => 'public'
])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full hover:shadow-md transition duration-200">

    @if($event->image)
        <img src="{{ asset('storage/' . $event->image) }}"
            class="h-48 w-full object-cover">
    @else
        <div class="h-48 bg-[#a3a3a3] text-white flex items-center justify-center text-sm font-medium">
            Event Image
        </div>
    @endif

    <div class="p-6 flex flex-col flex-grow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $event->title }}</h3>

        <div class="space-y-2 mb-6 flex-grow">
            <p class="text-sm text-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $event->location }}
            </p>

            <p class="text-sm text-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}
            </p>

            <p class="text-sm text-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                {{ $event->registrations_count ?? $event->registrations->count() }}/{{ $event->quota }} volunteers
            </p>
        </div>

        {{-- ================= PUBLIC ================= --}}
        @if($mode === 'public')
            @auth
                <a href="{{ route('events.show', $event->id) }}"
                   class="block w-full text-center bg-black text-white py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition">
                    View Details
                </a>
            @else
                <button onclick="openModal()"
                    class="block w-full text-center bg-black text-white py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition">
                    View Details
                </button>
            @endauth
        @endif

        {{-- ================= ORGANIZER ================= --}}
        @if($mode === 'manage')
            <div class="mt-auto space-y-2">
                <a href="{{ route('events.detail', $event->id) }}"
                   class="block w-full text-center bg-black text-white py-2.5 rounded-md text-sm font-medium hover:bg-gray-800 transition">
                    View Details
                </a>
                <a href="{{ route('events.participants', $event->id) }}"
                   class="block w-full text-center border border-gray-300 text-gray-700 py-2.5 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                    View Participants
                </a>
            </div>
        @endif
    </div>
</div>