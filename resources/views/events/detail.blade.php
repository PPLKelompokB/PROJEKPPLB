@extends('layouts.app')

@section('title', 'Detail Event')

@section('content')
<div class="max-w-4xl mx-auto mt-10 px-4">

    {{-- Card Event --}}
    <div class="bg-white rounded-lg shadow p-6">

        <h1 class="text-2xl font-bold mb-2">
            {{ $event->title ?? 'Nama Event' }}
        </h1>

        <p class="text-gray-600 mb-4">
            {{ $event->description ?? 'Deskripsi event belum tersedia.' }}
        </p>

        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mb-4">
            <div>
                <span class="font-semibold">Tanggal:</span><br>
                {{ $event->event_date ?? '-' }}
            </div>

            <div>
                <span class="font-semibold">Lokasi:</span><br>
                {{ $event->location ?? '-' }}
            </div>

            <div>
                <span class="font-semibold">Status:</span><br>
                {{ $event->status ?? 'Selesai' }}
            </div>

            <div>
                <span class="font-semibold">Organizer ID:</span><br>
                {{ $event->organizer_id ?? '-' }}
            </div>
        </div>

        {{-- Tombol Upload --}}
        <div class="mt-4">
            <a href="{{ route('documentation.create', $event->id) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block">
                Upload Dokumentasi
            </a>
        </div>

    </div>

</div>
@endsection