@props(['event', 'showActions' => true])

<div class="border p-4 rounded-lg mb-3">

    <div class="flex justify-between items-start">
        <div>
            <h3 class="font-semibold">{{ $event->title }}</h3>

            <p class="text-sm text-gray-500">
                {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
            </p>
        </div>

        <x-ui.status-badge :status="$event->volunteer_status ?? 'upcoming'" />
    </div>

    @if($showActions)
        <button class="mt-2 bg-black text-white px-3 py-1 rounded">
            View Details
        </button>
    @endif

</div>