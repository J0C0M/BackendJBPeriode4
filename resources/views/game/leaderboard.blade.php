@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 text-center mb-8">🏆 Leaderboard</h1>

                <!-- Filter Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="{{ route('leaderboard', ['period' => 'all_time']) }}"
                           class="py-2 px-1 border-b-2 font-medium text-sm {{ $period === 'all_time' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            All Time
                        </a>
                        <a href="{{ route('leaderboard', ['period' => 'monthly']) }}"
                           class="py-2 px-1 border-b-2 font-medium text-sm {{ $period === 'monthly' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            This Month
                        </a>
                        <a href="{{ route('leaderboard', ['period' => 'weekly']) }}"
                           class="py-2 px-1 border-b-2 font-medium text-sm {{ $period === 'weekly' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            This Week
                        </a>
                        <a href="{{ route('leaderboard', ['period' => 'daily']) }}"
                           class="py-2 px-1 border-b-2 font-medium text-sm {{ $period === 'daily' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Today
                        </a>
                    </nav>
                </div>

                @if($leaders->count() > 0)
                <!-- Top 3 Podium -->
                @if($leaders->count() >= 3)
                <div class="flex justify-center items-end space-x-8 mb-12">
                    <!-- Second Place -->
                    @if(isset($leaders[1]))
                    <div class="text-center">
                        <div class="relative">
                            @if($leaders[1]->avatar)
                            <img class="h-20 w-20 rounded-full object-cover mx-auto border-4 border-gray-300" src="{{ Storage::url($leaders[1]->avatar) }}" alt="{{ $leaders[1]->name }}">
                            @else
                            <div class="h-20 w-20 rounded-full bg-indigo-500 flex items-center justify-center text-white text-2xl mx-auto border-4 border-gray-300">
                                {{ strtoupper(substr($leaders[1]->name, 0, 1)) }}
                            </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 bg-gray-400 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold">2</div>
                        </div>
                        <div class="mt-4">
                            <h3 class="font-semibold text-gray-900">{{ $leaders[1]->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $leaders[1]->win_count }} wins</p>
                            <p class="text-xs text-gray-400">{{ number_format($leaders[1]->win_rate, 1) }}% win rate</p>
                        </div>
                        <div class="bg-gray-400 h-24 w-20 mx-auto mt-2 flex items-end justify-center text-white font-bold text-lg">
                            <span class="mb-2">🥈</span>
                        </div>
                    </div>
                    @endif

                    <!-- First Place -->
                    <div class="text-center">
                        <div class="relative">
                            @if($leaders[0]->avatar)
                            <img class="h-24 w-24 rounded-full object-cover mx-auto border-4 border-yellow-400" src="{{ Storage::url($leaders[0]->avatar) }}" alt="{{ $leaders[0]->name }}">
                            @else
                            <div class="h-24 w-24 rounded-full bg-indigo-500 flex items-center justify-center text-white text-3xl mx-auto border-4 border-yellow-400">
                                {{ strtoupper(substr($leaders[0]->name, 0, 1)) }}
                            </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 bg-yellow-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold">1</div>
                            <div class="absolute -top-2 left-1/2 transform -translate-x-1/2">
                                <span class="text-2xl">👑</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h3 class="font-bold text-lg text-gray-900">{{ $leaders[0]->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $leaders[0]->win_count }} wins</p>
                            <p class="text-xs text-gray-400">{{ number_format($leaders[0]->win_rate, 1) }}% win rate</p>
                        </div>
                        <div class="bg-yellow-500 h-32 w-20 mx-auto mt-2 flex items-end justify-center text-white font-bold text-xl">
                            <span class="mb-2">🥇</span>
                        </div>
                    </div>

                    <!-- Third Place -->
                    @if(isset($leaders[2]))
                    <div class="text-center">
                        <div class="relative">
                            @if($leaders[2]->avatar)
                            <img class="h-20 w-20 rounded-full object-cover mx-auto border-4 border-orange-400" src="{{ Storage::url($leaders[2]->avatar) }}" alt="{{ $leaders[2]->name }}">
                            @else
                            <div class="h-20 w-20 rounded-full bg-indigo-500 flex items-center justify-center text-white text-2xl mx-auto border-4 border-orange-400">
                                {{ strtoupper(substr($leaders[2]->name, 0, 1)) }}
                            </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold">3</div>
                        </div>
                        <div class="mt-4">
                            <h3 class="font-semibold text-gray-900">{{ $leaders[2]->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $leaders[2]->win_count }} wins</p>
                            <p class="text-xs text-gray-400">{{ number_format($leaders[2]->win_rate, 1) }}% win rate</p>
                        </div>
                        <div class="bg-orange-500 h-20 w-20 mx-auto mt-2 flex items-end justify-center text-white font-bold text-lg">
                            <span class="mb-2">🥉</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Full Rankings Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wins</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Games Played</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Win Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leaders as $index => $leader)
                        <tr class="hover:bg-gray-50 {{ auth()->check() && $leader->id === auth()->id() ? 'bg-indigo-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($index === 0)
                                    <span class="text-2xl">🥇</span>
                                    @elseif($index === 1)
                                    <span class="text-2xl">🥈</span>
                                    @elseif($index === 2)
                                    <span class="text-2xl">🥉</span>
                                    @else
                                    <span class="text-lg font-bold text-gray-600">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($leader->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($leader->avatar) }}" alt="{{ $leader->name }}">
                                    @else
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white">
                                        {{ strtoupper(substr($leader->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $leader->name }}
                                            @if(auth()->check() && $leader->id === auth()->id())
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">You</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ '@' . $leader->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $leader->win_count }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $leader->total_games }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($leader->win_rate, 1) }}%</div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $leader->win_rate }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('users.show', $leader) }}" class="text-indigo-600 hover:text-indigo-900">
                                    View Profile
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($leaders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-6">
                    {{ $leaders->appends(['period' => $period])->links() }}
                </div>
                @endif

                <!-- Your Ranking (if not in top visible) -->
                @auth
                @if($yourRank && $yourRank > $leaders->count())
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Your Ranking</h3>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-indigo-600">#{{ $yourRank }}</span>
                                @if(auth()->user()->avatar)
                                <img class="ml-4 h-10 w-10 rounded-full object-cover" src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                                @else
                                <div class="ml-4 h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-sm text-gray-500">{{ '@' . auth()->user()->username }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">{{ auth()->user()->games_won }} wins</div>
                                <div class="text-sm text-gray-500">{{ number_format(auth()->user()->getWinRateAttribute(), 1) }}% win rate</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endauth
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No rankings available</h3>
                    <p class="mt-1 text-sm text-gray-500">No games have been completed yet for this time period.</p>
                    @auth
                    <div class="mt-6">
                        <a href="{{ route('games.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Start Your First Game
                        </a>
                    </div>
                    @endauth
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
