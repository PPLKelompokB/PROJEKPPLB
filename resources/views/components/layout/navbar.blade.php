<nav class="flex justify-between items-center px-10 py-4 bg-white border-b border-gray-200">

    {{-- STYLE --}}
    <style>
        .nav-link {
            position: relative;
        }
        .nav-link-hover::after {
            content: "";
            display: block;
            height: 2px;
            background: black;
            transform: scaleX(0);
            transition: transform .3s;
        }
        .nav-link-hover:hover::after {
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

    <!-- LEFT: Logo -->
    <div class="flex items-center gap-2 w-1/4">
        <svg class="w-8 h-8 text-gray-800" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2"/>
            <path d="M6 10c1.5 0 2-1 3.5-1s2 1 3.5 1 2-1 3.5-1 2 1 3.5 1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
            <path d="M8 14c1.5 0 2-1 3.5-1s2 1 3.5 1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
        </svg>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">OceanCare</h1>
    </div>

    <!-- CENTER: Links -->
    <div class="flex items-center justify-center gap-8 w-2/4">
        @auth
            {{-- ========= VOLUNTEER ========= --}}
            @if(auth()->user()->role === 'volunteer')
                @php
                    $isDashboard = request()->is('dashboard') || request()->routeIs('volunteer.dashboard');
                    $isEvent = request()->is('events*') || request()->is('event*');
                @endphp
                <a href="{{ url('/dashboard') }}" 
                   class="flex items-center gap-2 text-sm font-medium transition {{ $isDashboard ? 'text-gray-800 bg-gray-200 px-4 py-2 rounded-lg' : 'text-gray-500 hover:text-gray-800' }}">
                    <svg class="w-4 h-4 {{ $isDashboard ? 'text-gray-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>

                <button class="flex items-center gap-1.5 text-sm font-bold border px-4 py-2 rounded-lg shadow-sm transition uppercase text-[10px] tracking-wider {{ $isEvent ? 'text-gray-800 border-gray-300 bg-gray-50' : 'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-800' }}">
                    EVENT
                    <svg class="w-3 h-3 {{ $isEvent ? 'text-gray-700' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <a href="#" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Points
                </a>

                <a href="#" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Certificates
                </a>

                <a href="#" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Leaderboard
                </a>
            @endif

            {{-- ========= ORGANIZER ========= --}}
            @if(auth()->user()->role === 'organizer')
                @php
                    $isDashboard = request()->is('dashboard') || request()->routeIs('organizer.dashboard');
                    $isManageEvent = request()->is('events*');
                    $isDoc = request()->is('documentation*');
                @endphp
                <a href="{{ route('organizer.dashboard') }}" 
                   class="flex items-center gap-2 text-sm font-medium transition {{ $isDashboard ? 'text-gray-800 bg-gray-200 px-4 py-2 rounded-lg' : 'text-gray-500 hover:text-gray-800' }}">
                    <svg class="w-4 h-4 {{ $isDashboard ? 'text-gray-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('events.manage') }}"
                   class="flex items-center gap-2 text-sm font-medium transition {{ $isManageEvent ? 'text-gray-800 bg-gray-200 px-4 py-2 rounded-lg' : 'text-gray-500 hover:text-gray-800' }}">
                    <svg class="w-4 h-4 {{ $isManageEvent ? 'text-gray-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Manage Event
                </a>

                <a href="#" class="flex items-center gap-2 text-sm font-medium transition {{ $isDoc ? 'text-gray-800 bg-gray-200 px-4 py-2 rounded-lg' : 'text-gray-500 hover:text-gray-800' }}">
                    <svg class="w-4 h-4 {{ $isDoc ? 'text-gray-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Event Documentation
                </a>
            @endif

            {{-- ========= ADMIN ========= --}}
            @if(auth()->user()->role === 'admin')
                @php
                    $isDashboard = request()->is('dashboard') || request()->routeIs('admin.dashboard');
                    $isDoc = request()->is('admin/documentation*');
                @endphp
                <a href="{{ url('/dashboard') }}" 
                   class="flex items-center gap-2 text-sm font-medium transition {{ $isDashboard ? 'text-gray-800 bg-gray-200 px-4 py-2 rounded-lg' : 'text-gray-500 hover:text-gray-800' }}">
                    <svg class="w-4 h-4 {{ $isDashboard ? 'text-gray-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="#" class="flex items-center gap-2 text-sm font-medium transition {{ $isDoc ? 'text-gray-800 bg-gray-200 px-4 py-2 rounded-lg' : 'text-gray-500 hover:text-gray-800' }}">
                    <svg class="w-4 h-4 {{ $isDoc ? 'text-gray-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Manage Documentation
                </a>
            @endif
        @endauth
    </div>

    <!-- RIGHT: Auth / Profile -->
    <div class="flex items-center justify-end gap-4 w-1/4">

        {{-- ================= GUEST ================= --}}
        @guest
            <a href="{{ route('login') }}" class="px-5 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-gray-800 transition">
                Login
            </a>
            <a href="{{ route('register') }}" class="px-5 py-2 bg-[#2c2c2c] text-white text-sm font-medium rounded-md hover:bg-gray-800 transition">
                Register
            </a>
        @endguest

        {{-- ================= AUTH ================= --}}
        @auth
            {{-- 🔔 NOTIFICATION (For Organizer) --}}
            @if(auth()->user()->role === 'organizer')
                <div class="relative" id="notifWrapper">
                    <button onclick="toggleNotif()" class="relative text-xl text-gray-500 hover:text-gray-900 transition mt-1 mr-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span id="notifBadge" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full hidden border border-white">
                            0
                        </span>
                    </button>

                    <div id="notifDropdown" class="absolute right-0 mt-3 w-80 bg-white shadow-xl rounded-xl border z-50 hidden">
                        <div class="p-4 border-b font-semibold text-sm">Notifications</div>
                        <div id="notifList" class="max-h-80 overflow-y-auto"><p class="p-4 text-sm text-gray-500">Loading...</p></div>
                    </div>
                </div>
            @endif

            {{-- ========= PROFILE ========= --}}
            <div class="flex items-center gap-3 border-l pl-4 border-gray-200">
                <img 
                    src="{{ auth()->user()->photo ? asset(auth()->user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}"
                    class="w-8 h-8 rounded-full border border-gray-200 shadow-sm"
                >

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 text-sm hover:text-red-600 transition font-medium">
                        Logout
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>