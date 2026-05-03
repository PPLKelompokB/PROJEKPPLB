@extends('layouts.app')

@section('title', 'Organizer Dashboard')

@section('content')
<div class="p-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold">
                Organizer Dashboard
            </h1>
            <p class="text-gray-500">
                Manage your events and track volunteer engagement
            </p>
        </div>

        <a href="/events/create" 
           class="bg-black text-white px-4 py-2 rounded">
            + Create New Event
        </a>
    </div>


    {{-- STATS --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <x-dashboard.card title="Total Events Created" :value="$totalEvents" />
        <x-dashboard.card title="Total Volunteers" :value="$totalVolunteers" />
        <x-dashboard.card title="Active Events" :value="$activeEvents" />
    </div>


    {{-- EVENT MANAGEMENT --}}
    <div class="bg-white p-6 rounded-xl shadow">

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold">Event Management</h2>

            <input type="text" placeholder="Search events..."
                class="border rounded px-3 py-2 text-sm">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="border-b text-gray-500">
                    <tr>
                        <th class="py-2">Event</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Volunteers</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($events as $event)
                        <tr class="border-b hover:bg-gray-50">

                            {{-- EVENT --}}
                            <td class="py-3">
                                <p class="font-medium">{{ $event->title }}</p>
                                <p class="text-gray-400 text-xs">
                                    {{ $event->description }}
                                </p>
                            </td>

                            {{-- DATE --}}
                            <td>
                                {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                            </td>

                            {{-- LOCATION --}}
                            <td>{{ $event->location }}</td>

                            {{-- VOLUNTEERS --}}
                            <td>
                                {{ $event->registrations->count() }} / {{ $event->quota }}
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <x-ui.status-badge :status="$event->status" />
                            </td>

                            {{-- ACTION --}}
                            <td class="text-right space-x-2">
                                <a href="/events/{{ $event->id }}/edit"
                                   class="text-blue-500 hover:underline text-sm">
                                    Edit
                                </a>

                                <form action="/events/{{ $event->id }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:underline text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">
                                Belum ada event
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>
@endsection