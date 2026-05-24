@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Full Volunteer Leaderboard</h1>
            <p class="text-gray-500 text-sm">Daftar lengkap seluruh pahlawan lingkungan OceanCare.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <form action="{{ route('leaderboard.full') }}" method="GET" class="flex items-center m-0">
                <div class="relative">
                    <select name="sort" onchange="this.form.submit()" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-10 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black shadow-sm cursor-pointer">
                        <option value="desc" {{ request('sort') == 'asc' ? '' : 'selected' }}>Top Ranked</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Lowest Ranked</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </form>
            
            <a href="{{ route('leaderboard') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                &larr; Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Volunteer</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Events</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Points</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($volunteers as $index => $volunteer)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php 
                                if(request('sort') == 'asc') {
                                    $rank = $volunteers->total() - ($volunteers->firstItem() + $index) + 1;
                                } else {
                                    $rank = $volunteers->firstItem() + $index; 
                                }
                            @endphp
                            
                            @if($rank == 1 && request('sort') != 'asc')
                                <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm">1</div>
                            @elseif($rank == 2 && request('sort') != 'asc')
                                <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-900 flex items-center justify-center font-bold text-sm">2</div>
                            @elseif($rank == 3 && request('sort') != 'asc')
                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-sm">3</div>
                            @else
                                <div class="w-8 h-8 flex items-center justify-center font-medium text-gray-500 text-sm">{{ $rank }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($volunteer->name) }}&background=F3F4F6&color=000000&size=128" class="w-10 h-10 rounded-full border border-gray-100">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $volunteer->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ \App\Models\Attendance::where('user_id', $volunteer->id)->count() }} events
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ number_format($volunteer->points) }} pts</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ $volunteer->location ?? 'Indonesia' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $volunteers->links() }}
        </div>
    </div>
</div>
@endsection