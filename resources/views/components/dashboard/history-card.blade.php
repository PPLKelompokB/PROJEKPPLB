@props(['event', 'points' => null])

<div class="flex justify-between items-center border-b py-2">
    <div>
        <p class="font-medium">{{ $event->title }}</p>
        <p class="text-sm text-gray-500">
            {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
        </p>
    </div>

    <div class="flex items-center gap-2">
        <x-ui.status-badge :status="$event->volunteer_status ?? 'completed'" />

        @if($points)
            <span class="text-green-600 text-sm">+{{ $points }} pts</span>
        @endif
    </div>
</div>