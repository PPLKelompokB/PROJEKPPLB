@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="p-8 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('events.manage') }}" class="text-sm text-gray-500 hover:underline">
            ← Back
        </a>

        <h1 class="text-2xl font-semibold mt-2">Edit Event</h1>

        <p class="text-gray-500 text-sm mt-1">
            Need to make some changes? No problem. Update your event details and keep everything running smoothly.
        </p>
    </div>

    {{-- CARD --}}
    <div class="bg-white rounded-xl shadow p-6">

        <form method="POST"
            action="{{ route('events.update', $event->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @csrf

            {{-- IMAGE UPLOAD --}}
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Event Image</label>

                <div class="border-2 border-dashed rounded-lg p-6 text-center text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-3xl">🖼️</span>
                        <p class="text-sm">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-400">PNG, JPG up to 5MB</p>

                        <input type="file" name="image" class="hidden" id="imageInput">

                        <button type="button"
                            onclick="document.getElementById('imageInput').click()"
                            class="mt-2 bg-black text-white px-4 py-1 rounded text-sm">
                            Choose File
                        </button>
                    </div>
                </div>
            </div>

            {{-- EVENT NAME --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Event Name</label>
                <input type="text" name="title"
                    value="{{ $event->title }}"
                    class="w-full border rounded px-3 py-2">
            </div>

            {{-- LOCATION --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Beach Location</label>
                <input type="text" name="location"
                    value="{{ $event->location }}"
                    class="w-full border rounded px-3 py-2">
            </div>

            {{-- DATE + TIME --}}
            <div class="grid grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Event Date</label>
                    <input type="date" name="date"
                        value="{{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Start Time *</label>
                    <input type="time" name="time"
                        value="{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }}"
                        class="w-full border rounded px-3 py-2">
                </div>

            </div>

            {{-- DURATION + QUOTA --}}
            <div class="grid grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Duration (hours)</label>
                    <select name="duration" class="w-full border rounded px-3 py-2">
                        @for($i=1; $i<=8; $i++)
                            <option value="{{ $i }}" {{ $event->duration == $i ? 'selected' : '' }}>
                                {{ $i }} Hours
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Volunteer Quota *</label>
                    <input type="number" name="quota"
                        value="{{ $event->quota }}"
                        class="w-full border rounded px-3 py-2">
                </div>

            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Event Description</label>
                <textarea name="description" rows="5"
                    class="w-full border rounded px-3 py-2">{{ $event->description }}</textarea>
            </div>

            {{-- MEETING POINT --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Meeting Point</label>
                <input type="text" name="meeting_point"
                    value="North Section of Palisade Park"
                    class="w-full border rounded px-3 py-2">
            </div>

            {{-- CONTACT --}}
            <div class="mb-6">
                <h2 class="font-semibold mb-2">Contact Information</h2>

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm mb-1">Contact Person</label>
                        <input type="text" name="contact_person"
                            value="John Doe"
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Phone Number</label>
                        <input type="text" name="phone"
                            value="+62 888 9898 9898"
                            class="w-full border rounded px-3 py-2">
                    </div>

                </div>
            </div>

            {{-- ACTION BUTTON --}}
            <div class="flex justify-end gap-3">

                <button type="button"
                    class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">
                    Save as Draft
                </button>

                <button type="submit"
                    class="px-5 py-2 bg-black text-white rounded">
                    Edit Event
                </button>

            </div>

        </form>

    </div>

</div>
@endsection