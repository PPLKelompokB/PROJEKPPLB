@extends('layouts.app')

@section('title', 'Detail Event Organizer')

@section('content')
<div class="max-w-4xl mx-auto mt-10 px-4">

    <div class="bg-white rounded-lg shadow p-6">

        <h1 class="text-2xl font-bold mb-2">
            {{ $event->title }}
        </h1>

        <p class="text-gray-600 mb-4">
            {{ $event->description }}
        </p>

        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mb-4">
            <div>
                <span class="font-semibold">Lokasi:</span><br>
                {{ $event->location }}
            </div>

            <div>
                <span class="font-semibold">Tanggal:</span><br>
                {{ $event->event_date ?? '-' }}
            </div>

            <div>
                <span class="font-semibold">Quota:</span><br>
                {{ $event->quota }}
            </div>
        </div>

        {{-- 🔥 BUTTON UPLOAD --}}
        <div class="mt-4">
            <a href="{{ route('documentation.create', $event->id) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block">
                Upload Dokumentasi
            </a>
        </div>

    </div>

</div>
@endsection