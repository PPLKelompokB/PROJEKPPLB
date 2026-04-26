<section class="py-12 px-10">
    <h2 class="text-2xl text-center mb-8 font-semibold">
        Featured Upcoming Events
    </h2>

    <div class="grid grid-cols-3 gap-6">
        @forelse($events as $event)
            <x-event-card :event="$event" />
        @empty
            <p class="text-center col-span-3">Belum ada event</p>
        @endforelse
    </div>
</section>