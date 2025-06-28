@extends('layouts.app')

@section('title', 'Friends')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Friends</h1>
                <p class="mt-2 text-gray-600">Connect with other Wordle players</p>
            </div>
            <a href="{{ route('friends.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Add Friends
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Friends</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $friends->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingRequests }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Games with Friends</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ auth()->user()->games()->where('game_type', 'friend')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Received Requests Section -->
        @if($receivedRequests->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Pending Friend Requests (Received)</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($receivedRequests as $request)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover" 
                             src="{{ $request->requester->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($request->requester->name) . '&color=7C3AED&background=EBF4FF' }}" 
                             alt="{{ $request->requester->name }}">
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $request->requester->name }}</p>
                            <p class="text-sm text-gray-500">@{{ $request->requester->username }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <form method="POST" action="{{ route('friends.accept', $request) }}">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-md text-sm hover:bg-green-700">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('friends.decline', $request) }}">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-md text-sm hover:bg-red-700">Decline</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <!-- Pending Sent Requests Section -->
        @if($sentRequests->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Pending Friend Requests (Sent)</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($sentRequests as $request)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover" 
                             src="{{ $request->addressee->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($request->addressee->name) . '&color=7C3AED&background=EBF4FF' }}" 
                             alt="{{ $request->addressee->name }}">
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $request->addressee->name }}</p>
                            <p class="text-sm text-gray-500">@{{ $request->addressee->username }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('friends.cancel', $request) }}">
                        @csrf
                        <button type="submit" class="bg-yellow-600 text-white px-3 py-1 rounded-md text-sm hover:bg-yellow-700">Cancel</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Friends List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Your Friends</h2>
            </div>
            
            @if($friends->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($friends as $friend)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        @if($friend->avatar)
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($friend->avatar) }}" alt="{{ $friend->name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">
                                {{ substr($friend->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $friend->name }}</p>
                            <p class="text-sm text-gray-500">@{{ $friend->username }}</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $friend->last_login_at ? 'Last seen ' . $friend->last_login_at->diffForHumans() : 'Never logged in' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('games.create') }}?friend={{ $friend->id }}" 
                           class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700">
                            Challenge
                        </a>
                        <a href="{{ route('users.show', $friend) }}" 
                           class="text-gray-600 hover:text-gray-900 px-3 py-1 rounded-md text-sm">
                            View Profile
                        </a>
                        <form action="{{ route('friends.destroy', $friend) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to remove this friend?')" 
                                    class="text-red-600 hover:text-red-900 px-3 py-1 rounded-md text-sm">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No friends yet</h3>
                <p class="mt-1 text-sm text-gray-500">Start by adding some friends to play Wordle together!</p>
                <div class="mt-6">
                    <a href="{{ route('friends.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Add Friends
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 