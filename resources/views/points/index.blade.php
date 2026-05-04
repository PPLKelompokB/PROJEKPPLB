@extends('layouts.app')

@section('title', 'My Points')

@section('content')
<div class="p-8">

    <h1 class="text-2xl font-semibold mb-2">
        My Points
    </h1>
    <p class="text-gray-500 mb-6">
        Track all your earned volunteer points
    </p>

    {{-- TOTAL POINT --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6 text-center">
        <h2 class="text-lg font-semibold">Total Points</h2>
        <p class="text-4xl font-bold text-green-600 mt-2">
            {{ $totalPoints }} pts
        </p>
    </div>

    {{-- HISTORY --}}
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Point History</h2>

        @forelse($points as $point)
            <div class="flex justify-between border-b py-3 text-sm">
                <div>
                    <p class="font-medium">
                        {{ $point->event->title ?? 'Event' }}
                    </p>
                    <p class="text-gray-500">
                        {{ $point->created_at->format('d M Y') }}
                    </p>
                </div>

                <div class="text-green-600 font-semibold">
                    +{{ $point->points }} pts
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm">Belum ada poin</p>
        @endforelse
    </div>

</div>
@endsection