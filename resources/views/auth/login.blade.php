<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | OceanCare</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

    <div class="text-center mb-6">
        <h1 class="text-xl font-bold">OceanCare</h1>
    </div>

    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold">Login</h2>
        <p class="text-sm text-gray-500">
            Please login to your account
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-700 p-3 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-3 rounded text-sm">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf

        <div>
            <label class="text-sm">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                placeholder="Enter your email"
                class="w-full mt-1 border p-3 rounded focus:outline-none focus:ring-2 focus:ring-black">
        </div>

        <div>
            <label class="text-sm">Password</label>
            <input type="password" name="password"
                placeholder="Enter your password"
                class="w-full mt-1 border p-3 rounded focus:outline-none focus:ring-2 focus:ring-black">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember">
                Remember me
            </label>

            <a href="#" class="text-gray-500 hover:underline">
                Forgot password?
            </a>
        </div>

        <button class="w-full bg-black text-white py-3 rounded hover:bg-gray-800 transition">
            Login
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-4">
        Don’t have an account?
        <a href="/register" class="text-black font-medium hover:underline">
            Sign up
        </a>
    </p>

</div>

</body>
</html>