<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register | OceanCare</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
                /* minimal CSS is already inlined on welcome page; if you build with Vite, this will use app assets */
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] min-h-screen flex items-center justify-center p-6 lg:p-8">
        <div class="w-full max-w-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] rounded-3xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
            <div class="px-8 py-10 lg:px-12 lg:py-12">
                <div class="mb-8 text-center">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#F53003] dark:text-[#FF4433]">OceanCare</p>
                    <h1 class="mt-4 text-3xl font-semibold">Daftar Akun Volunteer</h1>
                    <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">Buat akun untuk mulai bergabung dalam kegiatan pelestarian laut.</p>
                </div>

                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-[#f53003]/30 bg-[#fff2f2] p-4 text-sm text-[#1B1B18]">
                        <p class="font-semibold mb-2">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" class="space-y-5">
                    @csrf

                    <label class="block">
                        <span class="text-sm font-medium">Nama Lengkap</span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            class="mt-2 w-full rounded-md border border-gray-200 bg-white px-4 py-3 text-sm text-[#1b1b18] shadow-sm outline-none transition focus:border-[#f53003] focus:ring-2 focus:ring-[#f53003]/10"
                        />
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium">Email</span>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="mt-2 w-full rounded-md border border-gray-200 bg-white px-4 py-3 text-sm text-[#1b1b18] shadow-sm outline-none transition focus:border-[#f53003] focus:ring-2 focus:ring-[#f53003]/10"
                        />
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium">Kata Sandi</span>
                        <input
                            type="password"
                            name="password"
                            required
                            class="mt-2 w-full rounded-md border border-gray-200 bg-white px-4 py-3 text-sm text-[#1b1b18] shadow-sm outline-none transition focus:border-[#f53003] focus:ring-2 focus:ring-[#f53003]/10"
                        />
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium">Konfirmasi Kata Sandi</span>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            class="mt-2 w-full rounded-md border border-gray-200 bg-white px-4 py-3 text-sm text-[#1b1b18] shadow-sm outline-none transition focus:border-[#f53003] focus:ring-2 focus:ring-[#f53003]/10"
                        />
                    </label>

                    <button type="submit" class="w-full rounded-md bg-[#F53003] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#d12d03] focus:outline-none focus:ring-2 focus:ring-[#F53003]/50">
                        Daftar Sekarang
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    Sudah punya akun? <a href="{{ url('/') }}" class="font-medium text-[#F53003] hover:underline">Kembali ke Beranda</a>
                </p>
            </div>
        </div>
    </body>
</html>
