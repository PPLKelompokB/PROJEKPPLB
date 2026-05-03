@props([
    'event',
    'mode' => 'public'
])

<div class="bg-white rounded shadow">

    @if($event->image)
        <img src="{{ asset('storage/' . $event->image) }}"
            class="h-40 w-full object-cover">
    @else
        <div class="h-40 bg-gray-300 flex items-center justify-center">
            Event Image
        </div>
    @endif

    <div class="p-4">
        <h3 class="font-semibold">{{ $event->title }}</h3>

        <p class="text-sm text-gray-500">
            📍 {{ $event->location }}
        </p>

        <p class="text-sm text-gray-500">
            📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
        </p>

        <p class="text-sm text-gray-500">
            👥 {{ $event->registrations_count ?? $event->registrations->count() }}/{{ $event->quota }} volunteers
        </p>

        {{-- ================= PUBLIC ================= --}}
        @if($mode === 'public')

            @auth
                <a href="{{ route('events.show', $event->id) }}"
                   class="block mt-4 text-center bg-black text-white py-2 rounded">
                    View Details
                </a>
            @else
                <button onclick="openModal()"
                    class="block w-full mt-4 bg-gray-500 text-white py-2 rounded">
                    View Details
                </button>
            @endauth

        @endif


        {{-- ================= ORGANIZER ================= --}}
        @if($mode === 'manage')

            <div class="mt-4 space-y-2">

                <a href="{{ route('events.detail', $event->id) }}"
                   class="block text-center bg-black text-white py-2 rounded">
                    View Details
                </a>

                <a href="{{ route('events.participants', $event->id) }}"
                   class="block text-center border py-2 rounded hover:bg-gray-100">
                    View Participants
                </a>

            </div>

        @endif

    </div>
</div>