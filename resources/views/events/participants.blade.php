@extends('layouts.app')

@section('title', 'Event Participants - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- ✅ FLASH MESSAGE --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-3xl font-medium text-gray-900 tracking-tight">
            Event Participants
        </h1>
        <p class="text-sm text-gray-600 mt-2 font-medium">
            {{ $event->title }} - {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
        </p>
    </div>

    {{-- SELECT EVENT DROPDOWN --}}
    @if(isset($allEvents) && $allEvents->count() > 0)
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">Select Event</h2>
        <div class="relative w-full max-w-md">
            <select 
                class="w-full appearance-none bg-white border border-gray-300 text-gray-700 py-2.5 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400 text-sm font-medium transition cursor-pointer"
                onchange="window.location.href=this.value">
                @foreach($allEvents as $e)
                    <option value="{{ route('events.participants', $e->id) }}" {{ $e->id == $event->id ? 'selected' : '' }}>
                        {{ $e->title }} | {{ \Carbon\Carbon::parse($e->event_date)->format('M d, Y') }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLE SECTION --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-8">

        {{-- TOOLBAR (Search & Filters) --}}
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Search participants..."
                        class="border border-gray-200 rounded-lg pl-9 pr-4 py-2 text-sm w-64 focus:outline-none focus:ring-1 focus:ring-gray-300 transition">
                </div>

                <!-- Status Filter -->
                <div class="relative">
                    <select class="appearance-none bg-white border border-gray-200 text-gray-700 py-2 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-300 text-sm transition cursor-pointer">
                        <option>All Status</option>
                        <option>Confirmed</option>
                        <option>Present</option>
                        <option>Absent</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>

            <!-- Total Participants Label -->
            <div class="text-sm text-gray-500 font-medium">
                Total: {{ $participants->total() }} participants
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#fafafa] border-b border-gray-100">
                    <tr>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6 text-center">Volunteer</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6 text-center">Contact</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6 text-center">Registration Date</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6 text-center">Status</th>
                        <th class="text-[11px] font-bold text-gray-500 uppercase tracking-wider py-4 px-6 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($participants as $reg)
                        @php
                            $user = $reg->user;
                            $attendance = $attendanceMap[$user->id] ?? null;
                            
                            $isStarted = now() >= \Carbon\Carbon::parse($event->event_date);
                            $isFinished = now() > \Carbon\Carbon::parse($event->event_date)->addHours($event->duration);
                            
                            if ($attendance) {
                                $status = $attendance->status;
                            } else {
                                $status = $isStarted ? 'absent' : 'registered';
                            }
                        @endphp

                        <tr class="hover:bg-gray-50 transition duration-150">

                            {{-- VOLUNTEER INFO --}}
                            <td class="py-4 px-6 flex items-center gap-3">
                                <img src="{{ $user->photo ? asset($user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}"
                                    class="w-10 h-10 rounded-full border border-gray-200 shadow-sm">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">OceanCare Volunteer</p>
                                </div>
                            </td>

                            {{-- CONTACT --}}
                            <td class="py-4 px-6 text-center">
                                <p class="text-xs text-gray-800 font-medium">{{ $user->email }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $user->phone ?? 'No phone' }}</p>
                            </td>

                            {{-- DATE --}}
                            <td class="py-4 px-6 text-center text-xs text-gray-600 font-medium">
                                {{ $reg->created_at->format('F d, Y') }}
                            </td>

                            {{-- STATUS --}}
                            <td class="py-4 px-6 text-center">
                                @if($status === 'present')
                                    <span class="bg-gray-100 text-gray-700 border border-gray-200 px-3 py-1 rounded-full text-[10px] font-semibold tracking-wide capitalize">Present</span>
                                @elseif($status === 'absent')
                                    <span class="bg-gray-100 text-gray-500 border border-gray-200 px-3 py-1 rounded-full text-[10px] font-semibold tracking-wide capitalize">Absent</span>
                                @else
                                    <span class="bg-gray-50 text-gray-600 border border-gray-200 px-3 py-1 rounded-full text-[10px] font-semibold tracking-wide capitalize">Confirmed</span>
                                @endif
                            </td>

                            {{-- ACTION --}}
                            <td class="py-4 px-6 text-center">

                                {{-- EVENT BELUM TERJADI --}}
                                @if(!$isStarted)
                                    <span class="text-[11px] text-gray-400 font-medium">Event belum mulai</span>

                                {{-- EVENT SUDAH SELESAI --}}
                                @elseif($isFinished)
                                    <span class="inline-block text-[11px] font-semibold text-gray-600 border border-gray-200 px-4 py-1.5 rounded-lg bg-white shadow-sm">
                                        Event Selesai
                                    </span>

                                {{-- SUDAH PRESENT SECARA MANUAL --}}
                                @elseif(isset($attendance) && $attendance->status === 'present')
                                    <span class="inline-block text-[11px] font-semibold text-gray-600 border border-gray-200 px-4 py-1.5 rounded-lg bg-white shadow-sm">
                                        Already Marked
                                    </span>

                                {{-- BELUM DI MARK (SEDANG BERLANGSUNG) --}}
                                @else
                                    <div class="flex justify-center gap-2">
                                        <form method="POST" action="{{ route('attendance.mark', $reg->id) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="present">
                                            <button class="bg-black hover:bg-gray-800 text-white px-4 py-1.5 rounded-lg text-[11px] font-semibold shadow-sm transition">
                                                Mark Present
                                            </button>
                                        </form>
                                    </div>
                                @endif

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500 text-sm">
                                No participants registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($participants->hasPages())
        <div class="p-4 border-t border-gray-100 flex justify-between items-center bg-white">
            <p class="text-[11px] font-medium text-gray-500">
                Showing {{ $participants->firstItem() }} to {{ $participants->lastItem() }} of {{ $participants->total() }} participants
            </p>
            <div class="flex items-center gap-1.5">
                {{-- Previous --}}
                @if ($participants->onFirstPage())
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-400 cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                @else
                    <a href="{{ $participants->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-500 hover:bg-gray-50 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                @endif

                {{-- Pages --}}
                @foreach ($participants->getUrlRange(1, $participants->lastPage()) as $page => $url)
                    @if ($page == $participants->currentPage())
                        <button class="w-8 h-8 flex items-center justify-center bg-black text-white rounded font-medium text-xs shadow-sm">
                            {{ $page }}
                        </button>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 transition font-medium text-xs">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($participants->hasMorePages())
                    <a href="{{ $participants->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-500 hover:bg-gray-50 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-400 cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- BOTTOM STATS CARDS --}}
    @php
        $total = $participants->total();
        $present = \App\Models\Attendance::where('event_id', $event->id)->where('status', 'present')->count();
        $isEventStarted = now() >= \Carbon\Carbon::parse($event->event_date);
        
        if ($isEventStarted) {
            $absent = $total - $present;
        } else {
            $absent = \App\Models\Attendance::where('event_id', $event->id)->where('status', 'absent')->count();
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Total Registered -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Total Registered</p>
                <h2 class="text-3xl font-semibold text-gray-900">{{ $total }}</h2>
            </div>
            <div class="bg-gray-50 p-3 rounded-full border border-gray-100">
                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Present -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Present</p>
                <h2 class="text-3xl font-semibold text-gray-900">{{ $present }}</h2>
            </div>
            <div class="bg-gray-50 p-3 rounded-full border border-gray-100">
                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Attendance Rate</p>
                <h2 class="text-3xl font-semibold text-gray-900">
                    {{ $total ? round(($present / $total) * 100) : 0 }}%
                </h2>
            </div>
            <div class="bg-gray-50 p-3 rounded-full border border-gray-100">
                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
            </div>
        </div>

    </div>

</div>
@endsection