@extends('layouts.auth')

@section('title', 'Login')

@section('navbar')
{{-- kosong biar ga ada navbar --}}
@endsection

@section('footer')
{{-- kosong --}}
@endsection

@section('content')
<div class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        <div class="text-center mb-6">
            <h1 class="text-xl font-bold">OceanCare</h1>
        </div>

        <h2 class="text-2xl font-semibold text-center mb-2">Login</h2>
        <p class="text-sm text-gray-500 text-center mb-6">
            Please login to your account
        </p>

        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-700 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded text-sm">
                @foreach ($errors->all() as $error)
                    <p>- {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <input type="email" name="email" value="{{ old('email') }}"placeholder="Email"
                class="w-full border p-3 rounded" required>

            <input type="password" name="password" placeholder="Password"
                class="w-full border p-3 rounded" required>

            <button class="w-full bg-black text-white py-3 rounded">
                Login
            </button>
        </form>

        <p class="text-center text-sm mt-4">
            Don’t have an account?
            <a href="/register" class="font-medium hover:underline">Sign up</a>
        </p>

    </div>
</div>
@endsection