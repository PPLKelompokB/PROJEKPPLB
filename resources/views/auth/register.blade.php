<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | OceanCare</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

    <div class="text-center mb-6">
        <h1 class="text-xl font-bold">OceanCare</h1>
    </div>

    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold">Register</h2>
        <p class="text-sm text-gray-500">
            Please register by completing the information below
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
            <ul class="text-sm">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <p class="text-sm text-center text-gray-500 mb-2">Register as a</p>

            <div class="flex bg-gray-200 rounded p-1">
                <label class="flex-1 text-center cursor-pointer">
                    <input type="radio" name="role" value="organizer" class="hidden peer" required>
                    <div class="py-2 rounded peer-checked:bg-white peer-checked:shadow">
                        Organizer
                    </div>
                </label>

                <label class="flex-1 text-center cursor-pointer">
                    <input type="radio" name="role" value="volunteer" class="hidden peer">
                    <div class="py-2 rounded peer-checked:bg-white peer-checked:shadow">
                        Participant
                    </div>
                </label>
            </div>
        </div>

        <div>
            <label class="text-sm">Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                placeholder="Enter your name"
                class="w-full mt-1 border p-3 rounded focus:outline-none focus:ring-2 focus:ring-black">
        </div>

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

        <div>
            <label class="text-sm">Confirm Password</label>
            <input type="password" name="password_confirmation"
                placeholder="Enter your password"
                class="w-full mt-1 border p-3 rounded focus:outline-none focus:ring-2 focus:ring-black">
        </div>

        <button class="w-full bg-black text-white py-3 rounded hover:bg-gray-800 transition">
            Register
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-4">
        Already have an account?
        <a href="/login" class="text-black font-medium hover:underline">
            Sign in
        </a>
    </p>

</div>

</body>
</html>