@extends('layouts.app')

@section('title', 'Players')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Players</h1>
            <p class="text-gray-600 mt-2">Discover and connect with other Wordle players</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('friends.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                My Friends
            </a>
            <a href="{{ route('games.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Game
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('users.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by name or username..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                    <select id="sort" 
                            name="sort" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="games_won" {{ request('sort') == 'games_won' ? 'selected' : '' }}>Games Won</option>
                        <option value="win_rate" {{ request('sort') == 'win_rate' ? 'selected' : '' }}>Win Rate</option>
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Recently Joined</option>
                    </select>
                </div>
                
                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-700">Filter</label>
                    <select id="filter" 
                            name="filter" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="" {{ request('filter') == '' ? 'selected' : '' }}>All Players</option>
                        <option value="online" {{ request('filter') == 'online' ? 'selected' : '' }}>Online Now</option>
                        <option value="friends" {{ request('filter') == 'friends' ? 'selected' : '' }}>My Friends</option>
                        <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Recently Active</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Grid -->
    @if($users->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($users as $user)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                @if($user->avatar)
                                    <img class="h-12 w-12 rounded-full" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">@{{ $user->username }}</p>
                                </div>
                            </div>
                            
                            @if($user->id !== auth()->id())
                                <div class="flex space-x-2">
                                    @if(auth()->user()->isFriendWith($user))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Friend
                                        </span>
                                    @elseif(auth()->user()->hasSentFriendRequestTo($user))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Request Sent
                                        </span>
                                    @elseif(auth()->user()->hasReceivedFriendRequestFrom($user))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Request Received
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 text-sm mb-4">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">{{ $user->games_won }}</div>
                                <div class="text-gray-500">Wins</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">{{ $user->win_rate }}%</div>
                                <div class="text-gray-500">Win Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">{{ $user->games_played }}</div>
                                <div class="text-gray-500">Games</div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('users.show', $user) }}" 
                               class="flex-1 bg-gray-100 text-gray-700 text-center py-2 px-4 rounded-md hover:bg-gray-200 transition-colors text-sm font-medium">
                                View Profile
                            </a>
                            
                            @if($user->id !== auth()->id())
                                @if(!auth()->user()->isFriendWith($user) && !auth()->user()->hasSentFriendRequestTo($user) && !auth()->user()->hasReceivedFriendRequestFrom($user))
                                    <form method="POST" action="{{ route('friends.store') }}" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="friend_id" value="{{ $user->id }}">
                                        <button type="submit" 
                                                class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                                            Add Friend
                                        </button>
                                    </form>
                                @elseif(auth()->user()->hasReceivedFriendRequestFrom($user))
                                    <div class="flex-1 flex space-x-1">
                                        <form method="POST" action="{{ route('friends.accept', auth()->user()->getFriendshipWith($user)) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full bg-green-600 text-white py-2 px-2 rounded-md hover:bg-green-700 transition-colors text-xs font-medium">
                                                Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('friends.decline', auth()->user()->getFriendshipWith($user)) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full bg-red-600 text-white py-2 px-2 rounded-md hover:bg-red-700 transition-colors text-xs font-medium">
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('games.create') }}?opponent={{ $user->id }}" 
                                       class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                        Challenge
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $users->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No players found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterSelect = document.getElementById('filter');
    const sortSelect = document.getElementById('sort');
    
    filterSelect.addEventListener('change', function() {
        this.form.submit();
    });
    
    sortSelect.addEventListener('change', function() {
        this.form.submit();
    });
    
    // Search on Enter key
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });
});
</script>
@endsection 