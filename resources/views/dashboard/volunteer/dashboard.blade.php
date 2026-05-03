@extends('layouts.app')

@section('title', 'Volunteer Dashboard')

@section('content')
<div class="p-8">

    <h1 class="text-2xl font-semibold mb-2">
        Welcome back, {{ $user->name }}!
    </h1>
    <p class="text-gray-500 mb-6">
        Track your impact and manage your volunteer activities
    </p>

    {{-- STATS --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <x-dashboard.card title="Events Joined" :value="$totalEvents" />
        <x-dashboard.card title="Volunteer Points" :value="$user->points" />
        <x-dashboard.card title="Hours Volunteered" :value="$totalHours" />
        <x-dashboard.card title="Upcoming Events" :value="$upcomingEvents->count()" />
    </div>

    {{-- 🔥 TAMBAHAN: BOX TOTAL POINT (OPSIONAL, BIAR LEBIH JELAS) --}}
    <div class="bg-white p-4 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold">Total Poin Kamu</h2>
        <p class="text-3xl font-bold text-green-600">
            {{ $user->points }}
        </p>
    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- REGISTERED EVENTS --}}
        <div class="col-span-2 bg-white p-6 rounded-xl shadow">
            <h2 class="font-semibold mb-4">Registered Events</h2>

            @forelse($upcomingEvents as $reg)
                @if($reg->event)
                    <x-dashboard.event-card :event="$reg->event" />
                @endif
            @empty
                <p class="text-gray-500 text-sm">Belum ada event terdaftar</p>
            @endforelse
        </div>

        {{-- PROFILE --}}
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <img 
                src="{{ $user->photo ? asset($user->photo) : 'https://via.placeholder.com/80' }}"
                class="mx-auto rounded-full mb-3 w-20 h-20"
            >

            <h3 class="font-semibold">{{ $user->name }}</h3>
            <p class="text-gray-500 text-sm capitalize">{{ $user->role }}</p>

            <p class="mt-3 text-sm text-gray-500">
                Member since {{ $user->created_at->format('F Y') }}
            </p>
        </div>

    </div>

    {{-- HISTORY --}}
    <div class="mt-6 bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Event History</h2>

        @forelse($history as $item)
            @if($item->event)
                <x-dashboard.history-card :event="$item->event" />
            @endif
        @empty
            <p class="text-gray-500 text-sm">Belum ada riwayat event</p>
        @endforelse
    </div>
</div>
@endsection