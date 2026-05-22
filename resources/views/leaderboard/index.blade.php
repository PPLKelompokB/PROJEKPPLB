@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Volunteer Leaderboard</h1>
            <p class="text-gray-500 text-sm">Celebrate our top environmental champions and their impact.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Top Volunteers</h2>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Volunteer</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Events</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Points</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Recent Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($topVolunteers as $index => $volunteer)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($index == 0)
                                    <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm">1</div>
                                @elseif($index == 1)
                                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-900 flex items-center justify-center font-bold text-sm">2</div>
                                @elseif($index == 2)
                                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-sm">3</div>
                                @else
                                    <div class="w-8 h-8 flex items-center justify-center font-medium text-gray-500 text-sm">{{ $index + 1 }}</div>
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
                                {{ $volunteer->location ?? '' }} &bull; {{ rand(1, 5) }} days ago
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 text-center">
                <a href="{{ route('leaderboard.full') }}" class="text-sm font-medium text-gray-900 hover:text-gray-600 underline transition-colors">View Full Leaderboard</a>
            </div>
        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-6">Your Rank</h3>
                @auth
                    @if(Auth::user()->role === 'volunteer')
                        <div class="text-center mb-6">
                            <div class="text-5xl font-extrabold text-black mb-1">#{{ $userRank ?? '-' }}</div>
                            <p class="text-sm text-gray-500">You're in the top 12%!</p>
                        </div>
                        <div class="space-y-3 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Events Joined</span>
                            <span class="font-bold text-gray-900">{{ \App\Models\Attendance::where('user_id', Auth::id())->count() }}</span>
                        </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Total Points</span>
                                <span class="font-bold text-gray-900">{{ number_format(Auth::user()->points) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-sm text-gray-500">
                            Peringkat khusus untuk Volunteer.
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500 mb-3">Login to see your rank</p>
                        <a href="{{ route('login') }}" class="text-sm text-black font-semibold underline">Login &rarr;</a>
                    </div>
                @endauth
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-5">Achievements</h3>
                <div class="space-y-5">
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-gray-100 text-gray-900 p-2.5 rounded-lg shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Beach Guardian</h4>
                            <p class="text-xs text-gray-500 mt-0.5">{{ Auth::check() ? \App\Models\Attendance::where('user_id', Auth::id())->count() : 0 }} cleans completed</p>
                        </div>
                    </div>


                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Volunteers</span>
                        <span class="font-bold text-gray-900">{{ number_format($stats['total_volunteers']) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Events This Month</span>
                        <span class="font-bold text-gray-900">{{ number_format($stats['total_events'] ) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection