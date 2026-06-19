@extends('layouts.app')

@section('title', $registration->event->title)

@section('content')
@php
    $event = $registration->event;
    $isCompleted = \Carbon\Carbon::parse($event->event_date)->isPast();
    $statusText = $isCompleted ? 'Completed' : 'Upcoming';
    $statusColor = $isCompleted ? 'text-gray-600 bg-gray-100' : 'text-blue-600 bg-blue-50';
    if ($registration->status == 'cancelled') {
        $statusText = 'Cancelled';
        $statusColor = 'text-red-600 bg-red-50';
    }
@endphp
<div class="p-8 max-w-4xl mx-auto bg-gray-50 min-h-screen">
    
    <div class="mb-6">
        <a href="{{ route('volunteer.registered-events') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Registered Events
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- IMAGE --}}
        @if($event->image)
            <img src="{{ asset('storage/'.$event->image) }}" class="w-full h-[300px] object-cover">
        @else
            <div class="w-full h-[300px] bg-gray-300 flex items-center justify-center text-gray-500 text-lg font-medium">
                {{ $event->location ?? 'Beach Area' }}
            </div>
        @endif

        <div class="p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-[28px] font-bold text-gray-900 leading-tight mb-2">{{ $event->title }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                        Registration Status: {{ $statusText }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-4">
                    <div class="flex items-start gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <div>
                            <p class="font-medium text-gray-900">Date</p>
                            <p>{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-medium text-gray-900">Time & Duration</p>
                            <p>{{ \Carbon\Carbon::parse($event->event_date)->format('g:i A') }} ({{ $event->duration }} Hours)</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <div>
                            <p class="font-medium text-gray-900">Location</p>
                            <p>{{ $event->location }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <div>
                            <p class="font-medium text-gray-900">Quota</p>
                            <p>{{ $event->quota }} Volunteers</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <div>
                            <p class="font-medium text-gray-900">Contact Person</p>
                            <p>{{ $event->contact_person ?? 'Not specified' }} {{ $event->contact_phone ? '('.$event->contact_phone.')' : '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 mb-8">

            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Description</h3>
                <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                    {{ $event->description }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
