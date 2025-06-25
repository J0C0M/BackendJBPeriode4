@extends('layouts.app')

@section('title', 'Friends')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Friends</h1>
                        <a href="{{ route('friends.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Friends
                        </a>
                    </div>

                    <!-- Pending Requests Section -->
                    @if($receivedRequests->count() > 0 || $sentRequests->count() > 0)
                        <div class="mb-8">
                            @if($receivedRequests->count() > 0)
                                <div class="mb-6">
                                    <h2 class="text-lg font-medium text-gray-900 mb-4">Pending Friend Requests</h2>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($receivedRequests as $friendship)
                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    @if($friendship->requester->avatar)
                                                        <img class="h-12 w-12 rounded-full object-cover" src="{{ Storage::url($friendship->requester->avatar) }}" alt="{{ $friendship->requester->name }}">
                                                    @else
                                                        <div class="h-12 w-12 rounded-full bg-indigo-500 flex items-center justify-center text-white text-lg">
                                                            {{ strtoupper(substr($friendship->requester->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm font-medium text-gray-900">{{ $friendship->requester->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ '@' . $friendship->requester->username }}</p>
                                                        <p class="text-xs text-gray-400">{{ $friendship->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-4 flex space-x-2">
                                                    <form action="{{ route('friends.accept', $friendship) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        <button type="submit" class="w-full px-3 py-1 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                                                            Accept
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('friends.decline', $friendship) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        <button type="submit" class="w-full px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                                                            Decline
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($sentRequests->count() > 0)
                                <div class="mb-6">
                                    <h2 class="text-lg font-medium text-gray-900 mb-4">Sent Requests</h2>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($sentRequests as $friendship)
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    @if($friendship->addressee->avatar)
                                                        <img class="h-12 w-12 rounded-full object-cover" src="{{ Storage::url($friendship->addressee->avatar) }}" alt="{{ $friendship->addressee->name }}">
                                                    @else
                                                        <div class="h-12 w-12 rounded-full bg-indigo-500 flex items-center justify-center text-white text-lg">
                                                            {{ strtoupper(substr($friendship->addressee->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm font-medium text-gray-900">{{ $friendship->addressee->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ '@' . $friendship->addressee->username }}</p>
                                                        <p class="text-xs text-gray-400">Sent {{ $friendship->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Friends List -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">My Friends ({{ $friends->count() }})</h2>

                        @if($friends->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($friends as $friend)
                                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center">
                                            @if($friend->avatar)
                                                <img class="h-16 w-16 rounded-full object-cover" src="{{ Storage::url($friend->avatar) }}" alt="{{ $friend->name }}">
                                            @else
                                                <div class="h-16 w-16 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xl">
                                                    {{ strtoupper(substr($friend->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="ml-4 flex-1">
                                                <h3 class="text-lg font-medium text-gray-900">{{ $friend->name }}</h3>
                                                <p class="text-sm text-gray-500">{{ '@' . $friend->username }}</p>
                                                @if($friend->last_login_at)
                                                    <p class="text-xs text-gray-400">Last seen {{ $friend->last_login_at->diffForHumans() }}</p>
                                                @else
                                                    <p class="text-xs text-gray-400">Never logged in</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <div class="grid grid-cols-3 gap-4 text-center">
                                                <div>
                                                    <p class="text-lg font-semibold text-gray-900">{{ $friend->games_won }}</p>
                                                    <p class="text-xs text-gray-500">Wins</p>
                                                </div>
                                                <div>
                                                    <p class="text-lg font-semibold text-gray-900">{{ $friend->games_lost }}</p>
                                                    <p class="text-xs text-gray-500">Losses</p>
                                                </div>
                                                <div>
                                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($friend->getWinRateAttribute(), 1) }}%</p>
                                                    <p class="text-xs text-gray-500">Win Rate</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex space-x-2">
                                            <a href="{{ route('users.show', $friend) }}" class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded text-center hover:bg-gray-200">
                                                View Profile
                                            </a>
                                            <a href="{{ route('games.create', ['friend' => $friend->id]) }}" class="flex-1 px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded text-center hover:bg-indigo-700">
                                                Challenge
                                            </a>
                                        </div>

                                        <!-- Remove Friend -->
                                        <div class="mt-2">
                                            <form action="{{ route('friends.destroy', $friend) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this friend?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-3 py-1 text-red-600 text-sm hover:bg-red-50 rounded">
                                                    Remove Friend
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No friends yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding some friends to play with.</p>
                                <div class="mt-6">
                                    <a href="{{ route('friends.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Friends
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection<?php
