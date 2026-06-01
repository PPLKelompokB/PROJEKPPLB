@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<div class="p-8 max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-8 mt-4">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="text-xl text-gray-500 hover:text-black transition">
                &larr;
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Create New Event</h1>
        </div>
        <p class="text-gray-500 text-sm mt-2 ml-9">
            Organize a beach clean-up event to help protect our marine ecosystems
        </p>
    </div>

    {{-- CARD FORM --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">

        <form id="eventForm" action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- IMAGE UPLOAD --}}
            <div class="mb-6">
                <label class="block text-sm text-gray-700 mb-2">Event Image</label>

                <div class="border-[1.5px] border-dashed border-gray-300 rounded-lg p-10 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition cursor-pointer" onclick="document.getElementById('imageInput').click()">
                    <div class="flex flex-col items-center gap-1" id="uploadPlaceholder">
                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-400 mb-3">PNG, JPG up to 5MB</p>

                        <button type="button"
                            class="bg-black text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-gray-800 transition">
                            Choose File
                        </button>
                    </div>
                    
                    <img id="imagePreview" class="hidden max-h-48 rounded shadow-sm">
                    <p id="fileName" class="hidden text-sm text-gray-600 mt-3 font-medium"></p>
                    
                    <input type="file" name="image" class="hidden" id="imageInput" accept="image/*">
                </div>
            </div>

            {{-- EVENT NAME --}}
            <div class="mb-5">
                <label class="block text-sm text-gray-700 mb-1.5">Event Name <span class="text-gray-500">*</span></label>
                <input type="text" name="title" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400 transition" placeholder="Enter event name" required>
            </div>

            {{-- LOCATION --}}
            <div class="mb-5 relative">
                <label class="block text-sm text-gray-700 mb-1.5">Beach Location <span class="text-gray-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="location" class="w-full border border-gray-300 rounded-md pl-10 pr-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400 transition" placeholder="Search for beach location" required>
                </div>
            </div>

            {{-- DATE & TIME --}}
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm text-gray-700 mb-1.5">Event Date <span class="text-gray-500">*</span></label>
                    <input type="date" name="date" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none text-gray-700 transition" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1.5">Start Time <span class="text-gray-500">*</span></label>
                    <input type="time" name="time" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none text-gray-700 transition" required>
                </div>
            </div>

            {{-- DURATION & QUOTA --}}
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm text-gray-700 mb-1.5">Duration (hours)</label>
                    <select name="duration" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none text-gray-700 transition">
                        <option value="">Select duration</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1.5">Volunteer Quota <span class="text-gray-500">*</span></label>
                    <input type="number" name="quota" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400 transition" placeholder="Maximum number of volunteers" required>
                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-5">
                <label class="block text-sm text-gray-700 mb-1.5">Event Description <span class="text-gray-500">*</span></label>
                <textarea name="description" rows="5" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400 transition resize-y"
                    placeholder="Describe the event, what volunteers can expect, what to bring, meeting point details, etc." required></textarea>
            </div>

            {{-- MEETING POINT --}}
            <div class="mb-8 relative">
                <label class="block text-sm text-gray-700 mb-1.5">Meeting Point</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="meeting_point" class="w-full border border-gray-300 rounded-md pl-10 pr-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400 transition" placeholder="Enter Meeting Point">
                </div>
            </div>

            {{-- CONTACT --}}
            <div class="mb-8">
                <h2 class="text-sm font-medium text-gray-800 mb-4">Contact Information</h2>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm text-gray-700 mb-1.5">Contact Person <span class="text-gray-500">*</span></label>
                        <input type="text" name="contact_person" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400 transition" placeholder="Full name">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1.5">Phone Number <span class="text-gray-500">*</span></label>
                        <input type="text" name="phone" class="w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none placeholder-gray-400 transition" placeholder="Phone number">
                    </div>
                </div>
            </div>

            {{-- BUTTONS --}}
            <div class="flex justify-end gap-3 mt-8">
                <button type="submit" name="action" value="draft" class="px-6 py-2.5 border border-gray-300 rounded-md text-gray-700 font-medium text-sm hover:bg-gray-50 transition">
                    Save as Draft
                </button>

                <button type="submit" name="action" value="publish" class="px-6 py-2.5 bg-black text-white rounded-md font-medium text-sm hover:bg-gray-800 transition">
                    Create Event
                </button>
            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            let placeholder = document.getElementById('uploadPlaceholder');
            let preview = document.getElementById('imagePreview');
            let fileName = document.getElementById('fileName');
            
            placeholder.classList.add('hidden');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            
            fileName.textContent = file.name;
            fileName.classList.remove('hidden');
        }
    });

    document.getElementById('eventForm').addEventListener('submit', function(e) {
        let quota = document.querySelector('input[name="quota"]').value;
        if (quota <= 0) {
            e.preventDefault();
            alert('Data volunteer quota harus lebih dari 0');
        }
    });
</script>
@endpush
@endsection