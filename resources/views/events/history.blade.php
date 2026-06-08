@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Event History</h1>
        <p class="text-gray-500 text-sm">View the beach clean-up events you have participated in.</p>
    </div>

    <form action="{{ route('events.history') }}" method="GET" class="flex flex-col md:flex-row justify-between gap-4 mb-8">
        <div class="relative flex-1 md:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" onchange="this.form.submit()" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-black focus:border-black shadow-sm" placeholder="Search events...">
        </div>

        <div class="flex items-center gap-3">
            <div class="relative w-36">
                <select name="year" onchange="this.form.submit()" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2.5 pl-4 pr-10 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black shadow-sm cursor-pointer">
                    <option value="all" {{ request('year') == 'all' ? 'selected' : '' }}>All Years</option>
                    <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                    <option value="2025" {{ request('year') == '2025' ? 'selected' : '' }}>2025</option>
                    <option value="2024" {{ request('year') == '2024' ? 'selected' : '' }}>2024</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="relative w-40">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                </div>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($histories as $history)
            @php
                $event = $history->event;
                $isAttended = \App\Models\Attendance::where('event_id', $event->id)->where('user_id', auth()->id())->exists();
                $hasCertificate = $isAttended; 
            @endphp
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col transition hover:shadow-md">
                
                <div class="relative h-48 w-full bg-gray-200">
                    <img src="{{ $event->image ? asset('storage/' . $event->image) : 'https://images.unsplash.com/photo-1618477461853-cf6ed80fbea5?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $event->title }}" class="w-full h-full object-cover {{ !$isAttended ? 'grayscale-[50%] opacity-80' : '' }}">
                </div>

                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start gap-3 mb-4">
                        <h3 class="text-lg font-bold text-gray-900 leading-tight line-clamp-2">{{ $event->title }}</h3>
                        
                        @if($isAttended)
                            <span class="shrink-0 text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded">Present</span>
                        @else
                            <span class="shrink-0 text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">Absent</span>
                        @endif
                    </div>

                    <div class="space-y-2.5 mb-6">
                        <div class="flex items-start gap-2.5 text-sm text-gray-600">
                            <svg class="w-4.5 h-4.5 mt-0.5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="line-clamp-1">{{ $event->location }}</span>
                        </div>
                        
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4.5 h-4.5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}</span>
                        </div>

                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4.5 h-4.5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Role: Volunteer</span>
                        </div>

                        <div class="flex items-center gap-2.5 text-sm {{ $isAttended ? 'text-gray-900 font-medium' : 'text-gray-400' }}">
                            <svg class="w-4.5 h-4.5 {{ $isAttended ? 'text-black' : 'text-gray-300' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            <span>{{ $isAttended ? '10 Points Earned' : '0 Points' }}</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 flex items-center gap-3">
                        <a href="{{ route('events.show', $event->id) }}" class="flex-1 flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-black hover:bg-gray-800 focus:outline-none transition">
                            View Details
                        </a>
                        
                        @if($hasCertificate)
                            <a href="#" class="flex justify-center items-center py-2 px-3 border border-gray-300 shadow-sm rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        @else
                            <button disabled class="flex justify-center items-center py-2 px-3 border border-gray-200 shadow-sm rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-xl border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada History</h3>
                <p class="mt-1 text-sm text-gray-500">Kamu belum pernah berpartisipasi dalam event yang sudah selesai.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $histories->links() }}
    </div>

</div>
@endsection