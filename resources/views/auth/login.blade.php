@extends('layouts.app')

@section('title', 'Login - OceanCare')

@section('content')

<!-- LANDING PAGE BACKGROUND (BLURRED) -->
<div class="relative">
    <div class="filter blur-sm pointer-events-none select-none h-screen overflow-hidden">
        <x-landing.hero />
        <x-landing.stats :volunteers="$totalVolunteers" :events="$totalEvents" />
        <x-landing.events-section :events="$events" />
        <x-landing.mission />
    </div>

    <!-- LOGIN OVERLAY -->
    <div class="fixed inset-0 flex items-center justify-center z-50 overflow-y-auto">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <div class="relative bg-white rounded-3xl shadow-2xl w-[26rem] flex flex-col p-8 my-8 mx-4">
            
            <!-- Logo Section -->
            <div class="flex flex-col items-center mb-6">
                <svg class="w-10 h-10 text-gray-800" viewBox="0 0 100 100" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M 50 25 C 40 5, 10 5, 10 35 C 10 55, 35 75, 50 90 C 65 75, 90 55, 90 35 C 90 5, 60 5, 50 25 Z" fill="none" stroke="currentColor" stroke-width="10" stroke-linejoin="round"/>
                    <path d="M 13 48 C 25 35, 40 35, 50 48 C 60 61, 75 61, 87 48" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                    <path d="M 23 68 C 32 58, 42 58, 50 68 C 58 78, 68 78, 77 68" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                </svg>
                <h2 class="text-lg font-bold text-gray-800 tracking-tight mt-1">OceanCare</h2>
            </div>

            <h1 class="text-2xl font-bold text-center text-gray-900 mb-2">Welcome Back</h1>
            <p class="text-center text-[11px] text-gray-500 mb-6 font-medium max-w-xs mx-auto">
                Sign in to your OceanCare account
            </p>

            @if(session('success'))
                <div class="mb-4 bg-green-50 text-green-700 p-3 rounded-lg text-xs font-medium border border-green-100 text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-xs font-medium border border-red-100">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

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

                <!-- Forgot Password -->
                <div class="flex items-center justify-end mt-2 mb-4">
                    <a href="#" class="text-[11px] font-bold text-gray-800 hover:underline">
                        Forgot password?
                    </a>
                </div>

                <div class="pt-2">
                    <button class="w-full bg-black hover:bg-gray-800 text-white py-3 rounded-lg text-sm font-medium transition duration-200">
                        Login
                    </button>
                </div>
            </form>

            <p class="text-center text-xs text-gray-500 mt-6 font-medium">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-black font-bold hover:underline ml-1">
                    Sign up
                </a>
            </p>
        </div>
    </div>
</div>
@endsection