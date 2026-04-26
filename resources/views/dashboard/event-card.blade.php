<div class="border p-4 rounded-lg mb-3">
    <h3 class="font-semibold">{{ $event->title }}</h3>
    <p class="text-sm text-gray-500">
        {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
    </p>

    <button class="mt-2 bg-black text-white px-3 py-1 rounded">
        View Details
    </button>
</div>