<nav class="flex justify-between items-center px-8 py-4 bg-white shadow">

    {{-- LOGO --}}
    <h1 class="text-xl font-bold">🌊 OceanCare</h1>

    {{-- MENU --}}
    <div class="flex items-center gap-6">

        @guest
            <a href="/login" class="px-4 py-2 bg-black text-white rounded">
                Login
            </a>
            <a href="/register" class="px-4 py-2 border rounded hover:bg-gray-100">
                Register
            </a>
        @endguest


        @auth

            {{-- VOLUNTEER --}}
            @if(auth()->user()->role === 'volunteer')
                <a href="/dashboard" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Dashboard</a>
                <a href="/events" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Events</a>
                <a href="/points" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Points</a>
                <a href="/certificates" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Certificates</a>
                <a href="/leaderboard" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Leaderboard</a>
            @endif


            {{-- ORGANIZER --}}
            @if(auth()->user()->role === 'organizer')
                <a href="/dashboard" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Dashboard</a>
                <a href="/events/manage" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Manage Event</a>
                <a href="/documentations" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Documentation Event</a>
            @endif


            {{-- ADMIN --}}
            @if(auth()->user()->role === 'admin')
                <a href="/dashboard" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Dashboard</a>
                <a href="/admin/documentations" class="relative after:block after:h-[2px] after:bg-black after:scale-x-0 hover:after:scale-x-100 after:transition-transform after:duration-300">Manage Documentation</a>
            @endif


            {{-- PROFILE --}}
            <div class="flex items-center gap-3 ml-6">

                <img 
                    src="{{ auth()->user()->photo ? asset(auth()->user()->photo) : 'https://via.placeholder.com/40' }}"
                    class="w-8 h-8 rounded-full"
                >

                <span class="text-sm">{{ auth()->user()->name }}</span>

                <form action="/logout" method="POST">
                    @csrf
                    <button class="text-red-500 text-sm hover:underline">
                        Logout
                    </button>
                </form>
            </div>

        @endauth

    </div>
</nav>