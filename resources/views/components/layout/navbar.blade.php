<nav class="flex justify-between items-center px-8 py-4 bg-white shadow">

    {{-- STYLE --}}
    <style>
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: "";
            display: block;
            height: 2px;
            background: black;
            transform: scaleX(0);
            transition: transform .3s;
        }
        .nav-link:hover::after {
            transform: scaleX(1);
        }

        /* 🔥 ANIMATION */
        .dropdown-enter {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
        }

        .dropdown-enter-active {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition: all 0.2s ease-out;
        }

        .dropdown-leave-active {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
            transition: all 0.15s ease-in;
        }
    </style>

    <h1 class="text-xl font-bold">🌊 OceanCare</h1>

    <div class="flex items-center gap-6">

        {{-- ================= GUEST ================= --}}
        @guest
            <a href="{{ route('login') }}" class="px-4 py-2 bg-black text-white rounded">
                Login
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2 border rounded hover:bg-gray-100">
                Register
            </a>
        @endguest


        {{-- ================= AUTH ================= --}}
        @auth
            {{-- ========= VOLUNTEER ========= --}}
            @if(auth()->user()->role === 'volunteer')
                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>

                {{-- ⚠️ BELUM ADA ROUTE → sementara pakai placeholder --}}
                <a href="#" class="nav-link">Events</a>
                <a href="#" class="nav-link">Points</a>
                <a href="#" class="nav-link">Certificates</a>
                <a href="#" class="nav-link">Leaderboard</a>
            @endif


            {{-- ========= ORGANIZER ========= --}}
            @if(auth()->user()->role === 'organizer')

                <a href="{{ route('organizer.dashboard') }}" class="nav-link">
                    Dashboard
                </a>

                <a href="{{ route('events.manage') }}"
                    class="nav-link {{ request()->routeIs('events.*') ? 'font-semibold' : '' }}">
                    Manage Event
                </a>

                {{-- ⚠️ BELUM ADA ROUTE --}}
                <a href="#" class="nav-link">Documentation Event</a>

                {{-- 🔔 NOTIFICATION --}}
                <div class="relative" id="notifWrapper">

                    <button onclick="toggleNotif()" class="relative text-xl">
                        🔔
                        <span id="notifBadge"
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1.5 rounded-full hidden">
                            0
                        </span>
                    </button>

                    <div id="notifDropdown"
                        class="absolute right-0 mt-3 w-80 bg-white shadow-xl rounded-xl border z-50 hidden">

                        <div class="p-4 border-b font-semibold">
                            Notifications
                        </div>

                        <div id="notifList" class="max-h-80 overflow-y-auto">
                            <p class="p-4 text-sm text-gray-500">Loading...</p>
                        </div>

                    </div>
                </div>
            @endif


            {{-- ========= ADMIN ========= --}}
            @if(auth()->user()->role === 'admin')

                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>

                {{-- ⚠️ BELUM ADA ROUTE --}}
                <a href="#" class="nav-link">Manage Documentation</a>

            @endif


            {{-- ========= PROFILE ========= --}}
            <div class="flex items-center gap-3 ml-6">

                <img 
                    src="{{ auth()->user()->photo ? asset(auth()->user()->photo) : 'https://via.placeholder.com/40' }}"
                    class="w-8 h-8 rounded-full"
                >

                <span class="text-sm">{{ auth()->user()->name }}</span>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-500 text-sm hover:text-red-700">
                        Logout
                    </button>
                </form>

            </div>

        @endauth

    </div>
</nav>
