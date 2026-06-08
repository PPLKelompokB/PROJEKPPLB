@extends('layouts.app')

@section('title', 'User Profile - OceanCare')

@section('content')
<div class="px-10 py-8 max-w-4xl mx-auto">

    <!-- Back to Dashboard and Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Profile</h1>
            <p class="text-gray-500 mt-1 text-sm">Manage your personal details and account settings.</p>
        </div>
        <div>
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 text-sm rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Card Header Background Pattern -->
        <div class="h-32 bg-gradient-to-r from-blue-400 to-indigo-500 relative">
            <div class="absolute -bottom-12 left-8">
                <div class="relative w-24 h-24 rounded-full border-4 border-white bg-white shadow-md overflow-hidden">
                    <img 
                        src="{{ $user->photo_profile ? asset($user->photo_profile) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}"
                        alt="{{ $user->name }}" 
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>
        </div>

        <div class="pt-16 pb-8 px-8">
            <!-- Basic User Info -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-gray-100">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ $user->name }}</h2>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                            {{ $user->role === 'admin' ? 'bg-red-50 text-red-700 border border-red-100' : '' }}
                            {{ $user->role === 'organizer' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : '' }}
                            {{ $user->role === 'volunteer' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                        ">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-black text-white hover:bg-gray-800 transition text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>

            <!-- Profile Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="flex items-start gap-3 p-4 bg-gray-50/50 border border-gray-100 rounded-xl">
                    <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm text-gray-500 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Full Name</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $user->name }}</p>
                    </div>
                </div>

                <!-- Email Address -->
                <div class="flex items-start gap-3 p-4 bg-gray-50/50 border border-gray-100 rounded-xl">
                    <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm text-gray-500 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email Address</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $user->email }}</p>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="flex items-start gap-3 p-4 bg-gray-50/50 border border-gray-100 rounded-xl">
                    <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm text-gray-500 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone Number</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1 {{ !$user->phone ? 'text-gray-400 italic' : '' }}">
                            {{ $user->phone ?? 'Not specified' }}
                        </p>
                    </div>
                </div>

                <!-- Location -->
                <div class="flex items-start gap-3 p-4 bg-gray-50/50 border border-gray-100 rounded-xl">
                    <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm text-gray-500 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Location</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1 {{ !$user->location ? 'text-gray-400 italic' : '' }}">
                            {{ $user->location ?? 'Not specified' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
