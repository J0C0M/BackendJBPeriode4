@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Leaderboard</h1>
        <p class="text-gray-600">See who's dominating the Wordle competition</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ number_format($stats['total_players']) }}</div>
            <div class="text-gray-600">Total Players</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['total_games']) }}</div>
            <div class="text-gray-600">Games Played</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ number_format($stats['games_today']) }}</div>
            <div class="text-gray-600">Games Today</div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ number_format($stats['active_games']) }}</div>
            <div class="text-gray-600">Active Games</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8">
            <button class="tab-button active py-2 px-1 border-b-2 border-green-500 font-medium text-sm text-green-600" data-tab="all-time">
                All Time
            </button>
            <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" data-tab="weekly">
                This Week
            </button>
            <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" data-tab="daily">
                Today
            </button>
        </nav>
    </div>

    <!-- All Time Leaderboard -->
    <div id="all-time-tab" class="tab-content">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">All Time Champions</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Players with the most wins ever</p>
            </div>
            
            @if($leaderboards['all_time']->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($leaderboards['all_time'] as $index => $player)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($index < 3)
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold
                                                {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-400' : 'bg-orange-500') }}">
                                                {{ $index + 1 }}
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                                {{ $index + 1 }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center">
                                        @if($player->avatar)
                                            <img class="h-10 w-10 rounded-full mr-3" src="{{ Storage::url($player->avatar) }}" alt="{{ $player->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold mr-3">
                                                {{ substr($player->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $player->name }}</div>
                                            <div class="text-sm text-gray-500">@{{ $player->username }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900">{{ $player->games_won }}</div>
                                    <div class="text-sm text-gray-500">wins</div>
                                </div>
                            </div>
                            
                            <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <div class="text-gray-500">Games Played</div>
                                    <div class="font-medium">{{ $player->games_played }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Win Rate</div>
                                    <div class="font-medium">{{ $player->win_rate }}%</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Best Streak</div>
                                    <div class="font-medium">{{ $player->best_streak }}</div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No players yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Start playing to see the leaderboard!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Weekly Leaderboard -->
    <div id="weekly-tab" class="tab-content hidden">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">This Week's Winners</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Players with the most wins this week</p>
            </div>
            
            @if($leaderboards['weekly']->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($leaderboards['weekly'] as $index => $player)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($index < 3)
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold
                                                {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-400' : 'bg-orange-500') }}">
                                                {{ $index + 1 }}
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                                {{ $index + 1 }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center">
                                        @if($player->avatar)
                                            <img class="h-10 w-10 rounded-full mr-3" src="{{ Storage::url($player->avatar) }}" alt="{{ $player->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold mr-3">
                                                {{ substr($player->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $player->name }}</div>
                                            <div class="text-sm text-gray-500">@{{ $player->username }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-lg font-bold text-blue-600">{{ $player->weekly_wins }}</div>
                                    <div class="text-sm text-gray-500">wins this week</div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No games this week</h3>
                    <p class="mt-1 text-sm text-gray-500">Start playing to see this week's leaderboard!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Daily Leaderboard -->
    <div id="daily-tab" class="tab-content hidden">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Today's Champions</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Players with the most wins today</p>
            </div>
            
            @if($leaderboards['daily']->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($leaderboards['daily'] as $index => $player)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($index < 3)
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold
                                                {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-400' : 'bg-orange-500') }}">
                                                {{ $index + 1 }}
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                                {{ $index + 1 }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center">
                                        @if($player->avatar)
                                            <img class="h-10 w-10 rounded-full mr-3" src="{{ Storage::url($player->avatar) }}" alt="{{ $player->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold mr-3">
                                                {{ substr($player->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $player->name }}</div>
                                            <div class="text-sm text-gray-500">@{{ $player->username }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-600">{{ $player->daily_wins }}</div>
                                    <div class="text-sm text-gray-500">wins today</div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No games today</h3>
                    <p class="mt-1 text-sm text-gray-500">Start playing to see today's leaderboard!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest game results and achievements</p>
        </div>
        
        @if($recentActivity->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($recentActivity as $activity)
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-3">
                                    @if($activity->winner->avatar)
                                        <img class="h-8 w-8 rounded-full" src="{{ Storage::url($activity->winner->avatar) }}" alt="{{ $activity->winner->name }}">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                                            {{ substr($activity->winner->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <span class="text-green-600">{{ $activity->winner->name }}</span> won against {{ $activity->loser->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">in {{ $activity->attempts }} attempts</div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500">No recent activity</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-green-500', 'text-green-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Add active class to clicked button
            this.classList.add('active', 'border-green-500', 'text-green-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            // Show target content
            document.getElementById(targetTab + '-tab').classList.remove('hidden');
        });
    });
});
</script>
@endsection 