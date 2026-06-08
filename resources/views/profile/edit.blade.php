@extends('layouts.app')

@section('title', 'Edit Profile - OceanCare')

@section('content')
<div class="px-10 py-8 max-w-4xl mx-auto">

    <!-- Breadcrumb and Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Profile</h1>
            <p class="text-gray-500 mt-1 text-sm">Update your avatar and personal details below.</p>
        </div>
        <div>
            <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition shadow-sm">
                Cancel
            </a>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-8">
            <!-- Form Grid -->
            <div class="flex flex-col md:flex-row gap-8">
                
                <!-- Left: Avatar Upload Section -->
                <div class="w-full md:w-1/3 flex flex-col items-center border-b md:border-b-0 md:border-r border-gray-100 pb-8 md:pb-0 md:pr-8">
                    <h3 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-wider self-start">Profile Picture</h3>
                    
                    <!-- Interactive Avatar Preview -->
                    <div class="relative group cursor-pointer w-40 h-40 rounded-full border border-gray-200 shadow-inner overflow-hidden mb-4" id="avatarContainer">
                        <img 
                            id="avatarPreview"
                            src="{{ $user->photo_profile ? asset($user->photo_profile) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}"
                            alt="Preview Photo" 
                            class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                        >
                        
                        <!-- Overlay on Hover -->
                        <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition duration-300">
                            <svg class="w-6 h-6 mb-1 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-xs font-semibold">Change Photo</span>
                        </div>
                    </div>
                    
                    <!-- Hidden File Input -->
                    <input 
                        type="file" 
                        id="photo_profile" 
                        name="photo_profile" 
                        class="hidden" 
                        accept="image/jpeg,image/png,image/jpg"
                    >

                    <button 
                        type="button" 
                        id="selectFileBtn" 
                        class="px-4 py-2 border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-lg shadow-sm transition"
                    >
                        Choose Image
                    </button>
                    
                    <p class="text-[11px] text-gray-400 mt-4 text-center leading-relaxed">
                        JPG, JPEG or PNG formats only.<br>Maximum size 2MB.
                    </p>
                    
                    <!-- Client-side Error Message -->
                    <p id="clientErrorMsg" class="text-xs text-red-500 mt-3 font-semibold text-center hidden"></p>

                    @error('photo_profile')
                        <p class="text-xs text-red-500 mt-3 font-semibold text-center">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Right: Form Fields Section -->
                <div class="flex-1 space-y-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Account Details</h3>

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Full Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $user->name) }}" 
                                class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none transition @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="Enter your full name" 
                                required
                            >
                        </div>
                        @error('name')
                            <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Email Address <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}" 
                                class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none transition @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="Enter your email address" 
                                required
                            >
                        </div>
                        @error('email')
                            <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Phone Number</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                id="phone" 
                                name="phone" 
                                value="{{ old('phone', $user->phone) }}" 
                                class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none transition @error('phone') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="e.g. 08123456789"
                            >
                        </div>
                        @error('phone')
                            <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Location</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                id="location" 
                                name="location" 
                                value="{{ old('location', $user->location) }}" 
                                class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none transition @error('location') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                placeholder="e.g. Jakarta, Indonesia"
                            >
                        </div>
                        @error('location')
                            <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('profile.show') }}" class="px-5 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition shadow-sm">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-200">
                Save Changes
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('photo_profile');
        const selectFileBtn = document.getElementById('selectFileBtn');
        const avatarContainer = document.getElementById('avatarContainer');
        const avatarPreview = document.getElementById('avatarPreview');
        const clientErrorMsg = document.getElementById('clientErrorMsg');

        // Trigger file input dialog on click of preview container or button
        avatarContainer.addEventListener('click', () => fileInput.click());
        selectFileBtn.addEventListener('click', () => fileInput.click());

        // File change listener
        fileInput.addEventListener('change', function(e) {
            clientErrorMsg.classList.add('hidden');
            
            const file = e.target.files[0];
            if (!file) return;

            // Client side validation
            const validMimeTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!validMimeTypes.includes(file.type)) {
                showError('Only JPG, JPEG, and PNG formats are allowed.');
                clearInput();
                return;
            }

            if (file.size > maxSize) {
                showError('Image size cannot exceed 2 MB.');
                clearInput();
                return;
            }

            // Real-time FileReader preview
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        function showError(message) {
            clientErrorMsg.innerText = message;
            clientErrorMsg.classList.remove('hidden');
        }

        function clearInput() {
            fileInput.value = '';
        }
    });
</script>
@endpush
@endsection
