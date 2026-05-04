<section class="py-20 px-6 md:px-10 lg:px-20 bg-[#f8f9fa]">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl text-center mb-12 font-semibold text-gray-800">
            Featured Upcoming Events
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <x-event.card :event="$event" />
            @empty
                <p class="text-center col-span-3 text-gray-500">No upcoming events right now. Check back soon!</p>
            @endforelse
        </div>
    </div>
</section>