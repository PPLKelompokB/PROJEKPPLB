@extends('layouts.app')

@section('title', 'Participants')

@section('content')
<div class="p-8 max-w-6xl mx-auto">

    {{-- ✅ FLASH MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <h1 class="text-xl font-semibold">Event Participants</h1>
    <p class="text-sm text-gray-500 mb-6">
        {{ $event->title }} - {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
    </p>

    {{-- TABLE --}}
    <div class="bg-white rounded shadow overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex justify-between items-center p-4 border-b">
            <p class="text-sm text-gray-500">
                Total: {{ $participants->total() }} participants
            </p>
        </div>

        {{-- TABLE --}}
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="text-left p-3">Volunteer</th>
                    <th class="text-left p-3">Contact</th>
                    <th class="text-left p-3">Registration Date</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($participants as $reg)

                    @php
                        $user = $reg->user;
                        $attendance = $attendanceMap[$user->id] ?? null;
                        $status = $attendance->status ?? 'registered';
                    @endphp

                    <tr class="border-t">

                        {{-- USER --}}
                        <td class="p-3 flex items-center gap-3">
                            <img src="{{ $user->photo ? asset($user->photo) : 'https://via.placeholder.com/40' }}"
                                class="w-10 h-10 rounded-full">

                            <div>
                                <p class="font-medium">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">Volunteer</p>
                            </div>
                        </td>

                        {{-- CONTACT --}}
                        <td class="p-3 text-gray-600">
                            <p>{{ $user->email }}</p>
                        </td>

                        {{-- DATE --}}
                        <td class="p-3 text-gray-600">
                            {{ $reg->created_at->format('F d, Y') }}
                        </td>

                        {{-- STATUS --}}
                        <td class="p-3">
                            @if($status === 'present')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">
                                    Present
                                </span>
                            @elseif($status === 'absent')
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">
                                    Absent
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded">
                                    Registered
                                </span>
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="p-3">

                            {{-- ⛔ EVENT BELUM TERJADI --}}
                            @if($event->event_date > now())
                                <span class="text-xs text-gray-400">
                                    Event belum berlangsung
                                </span>

                            {{-- ✅ SUDAH PRESENT --}}
                            @elseif($status === 'present')
                                <span class="text-xs text-gray-500 border px-3 py-1 rounded">
                                    Already Present
                                </span>

                            {{-- ❌ SUDAH ABSENT --}}
                            @elseif($status === 'absent')
                                <span class="text-xs text-gray-500 border px-3 py-1 rounded">
                                    Marked Absent
                                </span>

                            {{-- 🔥 BELUM DI MARK --}}
                            @else
                                <div class="flex gap-2">

                                    {{-- PRESENT --}}
                                    <form method="POST"
                                          action="{{ route('attendance.mark', $reg->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="present">

                                        <button class="bg-black text-white px-3 py-1 rounded text-xs">
                                            Present
                                        </button>
                                    </form>

                                    {{-- ABSENT --}}
                                    <form method="POST"
                                          action="{{ route('attendance.mark', $reg->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="absent">

                                        <button class="border px-3 py-1 rounded text-xs hover:bg-gray-100">
                                            Absent
                                        </button>
                                    </form>

                                </div>
                            @endif

                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="p-4 border-t">
            {{ $participants->links() }}
        </div>

    </div>

    {{-- STATS --}}
    @php
        $present = $participants->filter(fn($p) => optional($p->attendance)->status === 'present')->count();
        $absent = $participants->filter(fn($p) => optional($p->attendance)->status === 'absent')->count();
        $total = $participants->total();
    @endphp

    <div class="grid grid-cols-3 gap-4 mt-6">

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-gray-500 text-sm">Total Registered</p>
            <h2 class="text-xl font-semibold">{{ $total }}</h2>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-gray-500 text-sm">Present</p>
            <h2 class="text-xl font-semibold">{{ $present }}</h2>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-gray-500 text-sm">Attendance Rate</p>
            <h2 class="text-xl font-semibold">
                {{ $total ? round(($present / $total) * 100) : 0 }}%
            </h2>
        </div>

    </div>

</div>
@endsection