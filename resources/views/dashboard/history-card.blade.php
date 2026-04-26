<div class="flex justify-between items-center border-b py-2">
    <div>
        <p class="font-medium">{{ $event->title }}</p>
        <p class="text-sm text-gray-500">
            {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
        </p>
    </div>

    <span class="text-green-600 text-sm">+100 pts</span>
</div>