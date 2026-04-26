<div class="bg-white rounded shadow">
    <div class="h-40 bg-gray-300 flex items-center justify-center">
        Event Image
    </div>
    <div class="p-4">
        <h3 class="font-semibold">{{ $event->title }}</h3>

        <p class="text-sm text-gray-500">
            📍 {{ $event->location }}
        </p>

        <p class="text-sm text-gray-500">
            📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
        </p>

        <p class="text-sm text-gray-500">
            👥 {{ $event->registrations->count() }}/{{ $event->quota }} volunteers
        </p>

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
    </div>
</div>