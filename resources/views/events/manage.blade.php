@extends('layouts.app')

@section('title', 'Manage Events')

@section('content')
<div class="p-8">

    {{-- HEADER --}}
    <h1 class="text-2xl font-semibold mb-1">Manage Your Events</h1>
    <p class="text-gray-500 mb-6">
        Easily view, edit, and organize all your events in one place. Stay in control and keep everything up to date.
    </p>

    {{-- GRID --}}
    <div class="grid grid-cols-3 gap-6">
        @forelse($events as $event)
            <x-event.card :event="$event" mode="manage" />
        @empty
            <p class="text-gray-500">Belum ada event</p>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-8 flex justify-between items-center text-sm text-gray-500">
        <span>
            Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events
        </span>

        <div class="flex gap-2">
            @if ($events->onFirstPage())
                <span class="px-3 py-1 border rounded opacity-50">‹</span>
            @else
                <a href="{{ $events->previousPageUrl() }}" class="px-3 py-1 border rounded">‹</a>
            @endif

            @for ($i = 1; $i <= $events->lastPage(); $i++)
                <a href="{{ $events->url($i) }}"
                    class="px-3 py-1 border rounded {{ $events->currentPage() == $i ? 'bg-black text-white' : '' }}">
                    {{ $i }}
                </a>
            @endfor

            @if ($events->hasMorePages())
                <a href="{{ $events->nextPageUrl() }}" class="px-3 py-1 border rounded">›</a>
            @else
                <span class="px-3 py-1 border rounded opacity-50">›</span>
            @endif
        </div>

        <div>
            {{ $events->links() }}
        </div>
    </div>

</div>
@endsection