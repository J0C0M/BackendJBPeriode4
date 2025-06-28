@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-red-100">
                <span class="text-4xl">🎯</span>
            </div>
        </div>
        
        <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Page Not Found</h2>
        <p class="text-gray-600 mb-8">
            Oops! The page you're looking for doesn't exist. 
            Maybe you mistyped the URL or the page has been moved.
        </p>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Go Home
            </a>
            
            <div class="text-sm text-gray-500">
                <p>Or try one of these pages:</p>
                <div class="mt-2 space-x-4">
                    <a href="{{ route('games.index') }}" class="text-green-600 hover:text-green-500">Games</a>
                    <a href="{{ route('leaderboard.index') }}" class="text-green-600 hover:text-green-500">Leaderboard</a>
                    <a href="{{ route('users.index') }}" class="text-green-600 hover:text-green-500">Players</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 