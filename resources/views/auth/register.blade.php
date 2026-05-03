@extends('layouts.auth')

@section('title', 'Register')

@section('footer')
@endsection

@section('content')
<div class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        <div class="text-center mb-6">
            <h1 class="text-xl font-bold">OceanCare</h1>
        </div>

        <h2 class="text-2xl font-semibold text-center mb-4">Register</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded text-sm">
                @foreach ($errors->all() as $error)
                    <p>- {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf

            {{-- ROLE --}}
            <div class="flex bg-gray-200 rounded p-1">
                <label class="flex-1 text-center cursor-pointer">
                    <input type="radio" name="role" value="organizer" class="hidden peer" required>
                    <div class="py-2 rounded peer-checked:bg-white">Organizer</div>
                </label>

                <label class="flex-1 text-center cursor-pointer">
                    <input type="radio" name="role" value="volunteer" class="hidden peer" checked>
                    <div class="py-2 rounded peer-checked:bg-white peer-checked:font-semibold">Volunteer</div>
                </label>
            </div>

            <input type="text" name="name" value="{{ old('name') }}" placeholder="Name" class="w-full border p-3 rounded" required>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full border p-3 rounded" required>           
            <input type="password" name="password" placeholder="Password" class="w-full border p-3 rounded">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full border p-3 rounded">

            <button class="w-full bg-black text-white py-3 rounded">
                Register
            </button>
        </form>

        <p class="text-center text-sm mt-4">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium hover:underline">
                Login
            </a>
        </p>
    </div>
</div>
@endsection