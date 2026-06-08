@extends('layouts.app')

@section('title', 'Event Documentation - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- Error / Success messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
            Event Documentation
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Upload and manage photos from your beach clean-up events.
        </p>
    </div>

    {{-- Select Event --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Event</h2>
        <form method="GET" action="{{ route('documentation.index') }}" id="eventSelectForm">
            <select name="event_id" onchange="document.getElementById('eventSelectForm').submit()" class="w-full md:w-1/2 border border-gray-300 rounded-lg p-3 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none transition">
                <option value="">Select an Event...</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                        {{ $event->title }} | {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if(request('event_id') && $events->where('id', request('event_id'))->count() > 0)
    {{-- Upload Documentation --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload Documentation</h2>
        <form action="{{ route('documentation.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="event_id" value="{{ request('event_id') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photo</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:bg-gray-50 transition cursor-pointer relative">
                        <input type="file" name="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".jpg,.jpeg,.png" required onchange="previewFile(this)">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-1 text-sm text-gray-600">Drag and drop your photos here, or</p>
                        <button type="button" class="mt-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-medium hover:bg-gray-50">Choose Files</button>
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 10MB</p>
                        <p id="fileNameDisplay" class="mt-2 text-sm text-blue-600 font-medium hidden"></p>
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo Description</label>
                    <textarea name="note" rows="5" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none transition flex-grow" placeholder="Describe what's happening in this photo..."></textarea>
                    <button type="submit" class="mt-4 w-full bg-[#1a1c20] hover:bg-black text-white py-3 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload Documentation
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    {{-- Event Documentation List --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Documentation</h2>
        @php
            $filteredDocs = request('event_id') ? $documentations->where('event_id', request('event_id')) : $documentations;
        @endphp
        
        @if(count($filteredDocs) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($filteredDocs as $doc)
            <div class="border border-gray-200 rounded-xl overflow-hidden flex flex-col">
                <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
                    <img src="{{ Storage::url($doc->file_path) }}" alt="Documentation" class="w-full h-full object-cover">
                </div>
                <div class="p-4 flex-grow flex flex-col">
                    @if(!request('event_id'))
                        <span class="text-[10px] font-bold text-gray-500 uppercase mb-1">{{ $doc->event->title }}</span>
                    @endif
                    <p class="text-sm text-gray-800 flex-grow">{{ $doc->note ?: 'No description provided.' }}</p>
                    <p class="text-[11px] text-gray-500 mt-3">Uploaded: {{ $doc->created_at->format('M d, Y') }}</p>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('documentation.edit', $doc->id) }}" class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 py-2 rounded text-xs font-medium transition border border-gray-200 flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Edit
                        </a>
                        <form action="{{ route('documentation.destroy', $doc->id ?? 0) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this documentation?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-gray-50 hover:bg-red-50 hover:text-red-600 text-gray-700 py-2 rounded text-xs font-medium transition border border-gray-200 flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-10">
            <p class="text-sm text-gray-500">
                {{ request('event_id') ? 'No documentation uploaded for this event yet.' : 'No documentation uploaded yet. Select an event above to start uploading.' }}
            </p>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewFile(input) {
    const file = input.files[0];
    const display = document.getElementById('fileNameDisplay');
    if (file) {
        display.textContent = file.name;
        display.classList.remove('hidden');
    } else {
        display.textContent = '';
        display.classList.add('hidden');
    }
}
</script>
@endpush