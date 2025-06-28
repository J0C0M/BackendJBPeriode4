@extends('layouts.app')

@section('title', $user->name . ' - Profile')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- User Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-8">
                <div class="flex items-center space-x-8">
                    <img class="h-24 w-24 rounded-full object-cover border-4 border-gray-200" 
                         src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7C3AED&background=EBF4FF' }}" 
                         alt="{{ $user->name }}">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-lg text-gray-600">@{{ $user->username }}</p>
                        @if($user->bio)
                            <p class="mt-2 text-gray-700">{{ $user->bio }}</p>
                        @endif
                        <div class="flex items-center mt-4 space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_online ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->is_online ? 'Online' : 'Offline' }}
                            </span>
                            <span class="text-sm text-gray-500">Member since {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2">
                        @if(auth()->id() !== $user->id)
                            @if($friendshipStatus === 'none')
                                <button onclick="sendFriendRequest({{ $user->id }})" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Friend
                                </button>
                            @elseif($friendshipStatus === 'pending_sent')
                                <span class="text-yellow-600 text-sm">Friend Request Sent</span>
                            @elseif($friendshipStatus === 'pending_received')
                                <div class="flex space-x-2">
                                    <button onclick="acceptFriendRequest({{ $user->id }})" class="bg-green-600 text-white px-3 py-1 rounded-md text-sm hover:bg-green-700">
                                        Accept
                                    </button>
                                    <button onclick="declineFriendRequest({{ $user->id }})" class="bg-red-600 text-white px-3 py-1 rounded-md text-sm hover:bg-red-700">
                                        Decline
                                    </button>
                                </div>
                            @elseif($friendshipStatus === 'friends')
                                <a href="{{ route('games.create') }}?friend={{ $user->id }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Challenge
                                </a>
                            @endif
                        @endif
                        <a href="{{ route('games.create') }}?friend={{ $user->id }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                            Play Together
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Stats -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Statistics</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Games Played</span>
                                <span class="text-lg font-semibold text-gray-900">{{ $stats['total_games'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Games Won</span>
                                <span class="text-lg font-semibold text-green-600">{{ $stats['games_won'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Win Rate</span>
                                <span class="text-lg font-semibold text-blue-600">{{ $stats['win_rate'] ?? 0 }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Average Guesses</span>
                                <span class="text-lg font-semibold text-purple-600">{{ $stats['avg_guesses'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Current Streak</span>
                                <span class="text-lg font-semibold text-yellow-600">{{ $stats['current_streak'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Best Streak</span>
                                <span class="text-lg font-semibold text-orange-600">{{ $stats['best_streak'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Friends -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Friends</h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['friends_count'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Friends</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Recent Activity</h2>
                    </div>
                    <div class="p-6">
                        @if(isset($recentActivity) && count($recentActivity) > 0)
                            <div class="space-y-3">
                                @foreach($recentActivity as $activity)
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">{{ $activity['description'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center">No recent activity</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Game History -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Recent Games</h2>
                        <a href="{{ route('users.history', $user) }}" class="text-blue-600 hover:text-blue-900 text-sm">View All</a>
                    </div>
                    <div class="p-6">
                        @if(isset($recentGames) && count($recentGames) > 0)
                            <div class="space-y-4">
                                @foreach($recentGames as $game)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-mono font-bold text-gray-600">{{ strtoupper($game->word) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Word: {{ $game->word }}</p>
                                            <p class="text-xs text-gray-500">{{ $game->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $game->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($game->status === 'active' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($game->status) }}
                                        </span>
                                        @if($game->status === 'completed')
                                            <span class="text-sm text-gray-600">{{ $game->moves_count ?? 0 }} guesses</span>
                                        @endif
                                        <a href="{{ route('games.show', $game) }}" class="text-blue-600 hover:text-blue-900 text-sm">View</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No games yet</h3>
                                <p class="mt-1 text-sm text-gray-500">This user hasn't played any games.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Friend request functions
function sendFriendRequest(userId) {
    fetch('{{ route("friends.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Friend request sent successfully!');
            location.reload();
        } else {
            alert(data.message || 'Error sending friend request');
        }
    })
    .catch(error => {
        console.error('Error sending friend request:', error);
        alert('Error sending friend request. Please try again.');
    });
}

function acceptFriendRequest(userId) {
    // Find the friendship ID first
    fetch(`{{ route('friends.search') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ search: '{{ $user->username }}' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.users.length > 0) {
            const user = data.users.find(u => u.id === userId);
            if (user && user.friendship_status === 'pending_received') {
                // Find the friendship record
                fetch(`{{ route('friends.index') }}`)
                .then(response => response.text())
                .then(html => {
                    // Extract friendship ID from the page or make a direct request
                    // For now, we'll use a simpler approach
                    window.location.href = `{{ route('friends.index') }}`;
                });
            }
        }
    })
    .catch(error => {
        console.error('Error accepting friend request:', error);
        alert('Error accepting friend request. Please try again.');
    });
}

function declineFriendRequest(userId) {
    // Similar to accept, redirect to friends page for now
    window.location.href = `{{ route('friends.index') }}`;
}
</script>
@endsection 