<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="text-2xl">🎯</span>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Wordle Game</h3>
                    <p class="text-sm text-gray-500">Challenge your friends in multiplayer Wordle!</p>
                </div>
            </div>
            
            <div class="mt-4 md:mt-0 flex space-x-6">
                <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Home</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </a>
                
                <a href="{{ route('leaderboard.index') }}" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Leaderboard</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </a>
                
                <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Players</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </a>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} Wordle Game. All rights reserved.
                </p>
                
                <div class="mt-4 md:mt-0 flex space-x-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-gray-900">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-900">Terms of Service</a>
                    <a href="#" class="hover:text-gray-900">Contact</a>
                </div>
            </div>
        </div>
    </div>
</footer> 