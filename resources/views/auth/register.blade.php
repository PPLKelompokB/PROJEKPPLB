@extends('layouts.app')

@section('title', 'Register - OceanCare')

@section('content')

<!-- LANDING PAGE BACKGROUND (BLURRED) -->
<div class="relative">
    <div class="filter blur-sm pointer-events-none select-none h-screen overflow-hidden">
        <x-landing.hero />
        <x-landing.stats :volunteers="$totalVolunteers" :events="$totalEvents" />
        <x-landing.events-section :events="$events" />
        <x-landing.mission />
    </div>

    <!-- REGISTER OVERLAY -->
    <div class="fixed inset-0 flex items-center justify-center z-50 overflow-y-auto">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <div class="relative bg-white rounded-3xl shadow-2xl w-[26rem] flex flex-col p-8 my-8 mx-4">
            
            <!-- Logo Section -->
            <div class="flex flex-col items-center mb-6">
                <svg class="w-10 h-10 text-gray-800" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2"/>
                    <path d="M6 10c1.5 0 2-1 3.5-1s2 1 3.5 1 2-1 3.5-1 2 1 3.5 1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <path d="M8 14c1.5 0 2-1 3.5-1s2 1 3.5 1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
                <h2 class="text-lg font-bold text-gray-800 tracking-tight mt-1">OceanCare</h2>
            </div>

            <h1 class="text-2xl font-bold text-center text-gray-900 mb-2">Register</h1>
            <p class="text-center text-[11px] text-gray-500 mb-6 font-medium max-w-xs mx-auto">
                Thank you for joining us, please register by completing information below
            </p>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-xs font-medium border border-red-100">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <!-- Role Toggle -->
                <div class="text-center">
                    <p class="text-[10px] text-gray-500 mb-2 font-medium">Register as a</p>
                    <div class="flex border border-gray-300 rounded-lg p-0.5 max-w-[200px] mx-auto overflow-hidden">
                        <label class="flex-1 text-center cursor-pointer relative">
                            <input type="radio" name="role" value="organizer" class="peer sr-only" required>
                            <div class="py-1.5 text-xs text-gray-600 font-medium peer-checked:bg-white peer-checked:text-black peer-checked:shadow-sm peer-checked:border peer-checked:border-gray-200 transition-all rounded-md">
                                Organizer
                            </div>
                        </label>
                        <label class="flex-1 text-center cursor-pointer relative">
                            <input type="radio" name="role" value="volunteer" class="peer sr-only" checked>
                            <div class="py-1.5 text-xs text-gray-600 font-medium peer-checked:bg-white peer-checked:text-black peer-checked:shadow-sm peer-checked:border peer-checked:border-gray-200 transition-all rounded-md">
                                Participant
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Name Input -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter your name" class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 transition-colors" required>
                    </div>
                </div>

                <!-- Email Input -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 transition-colors" required>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" placeholder="Enter your password" class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 transition-colors" required>
                    </div>
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password_confirmation" placeholder="Enter your password" class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 transition-colors" required>
                    </div>
                </div>

                <div class="pt-2">
                    <button class="w-full bg-black hover:bg-gray-800 text-white py-3 rounded-lg text-sm font-medium transition duration-200">
                        Register
                    </button>
                </div>
            </form>

            <p class="text-center text-xs text-gray-500 mt-6 font-medium">
                Already have an account?
                <a href="{{ route('login') }}" class="text-black font-bold hover:underline ml-1">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>
@endsection