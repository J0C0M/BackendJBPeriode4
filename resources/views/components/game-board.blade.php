<div class="game-board" data-game-id="{{ $game->id ?? '' }}" data-max-attempts="{{ $game->max_attempts ?? 6 }}">
    <div class="flex flex-col items-center space-y-4">
        <!-- Game Status -->
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                @if(isset($game))
                    @if($game->status === 'pending')
                        Waiting for opponent...
                    @elseif($game->status === 'in_progress')
                        Game in Progress
                    @elseif($game->status === 'completed')
                        Game Complete
                    @else
                        Game Cancelled
                    @endif
                @else
                    Wordle Game
                @endif
            </h2>
            
            @if(isset($game) && $game->status === 'in_progress')
                <p class="text-gray-600">
                    @if(isset($currentPlayer) && $currentPlayer && $currentPlayer->id === auth()->id())
                        Your turn!
                    @else
                        {{ $currentPlayer ? $currentPlayer->name . "'s turn" : "Waiting..." }}
                    @endif
                </p>
            @endif
        </div>

        <!-- Game Grid -->
        <div class="grid grid-cols-5 gap-2 mb-6" id="wordle-grid">
            @for($row = 0; $row < ($game->max_attempts ?? 6); $row++)
                @for($col = 0; $col < 5; $col++)
                    <div class="wordle-tile" 
                         data-row="{{ $row }}" 
                         data-col="{{ $col }}"
                         id="tile-{{ $row }}-{{ $col }}">
                        @if(isset($moves) && isset($moves[$row]) && isset($moves[$row][$col]))
                            {{ $moves[$row][$col] }}
                        @endif
                    </div>
                @endfor
            @endfor
        </div>

        <!-- Input Section -->
        @if(isset($game) && $game->status === 'in_progress' && $game->canPlayerMakeMove(auth()->user()))
            <div class="w-full max-w-md">
                <form id="wordle-form" class="flex space-x-2">
                    @csrf
                    <input type="text" 
                           id="wordle-input" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent text-center text-xl font-bold uppercase tracking-widest"
                           maxlength="5" 
                           placeholder="Enter word"
                           autocomplete="off"
                           pattern="[A-Za-z]{5}"
                           title="Please enter exactly 5 letters">
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-semibold">
                        Guess
                    </button>
                </form>
                
                <div class="mt-2 text-center">
                    <p class="text-sm text-gray-500">
                        Attempts: <span id="attempts-count">{{ $game->getPlayerAttempts(auth()->user()) }}</span> / {{ $game->max_attempts }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Game Actions -->
        @if(isset($game))
            <div class="flex space-x-4 mt-4">
                @if($game->status === 'pending' && $game->player2_id === auth()->id())
                    <form method="POST" action="{{ route('games.accept', $game) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Accept Game
                        </button>
                    </form>
                    <form method="POST" action="{{ route('games.decline', $game) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Decline Game
                        </button>
                    </form>
                @endif
                
                @if($game->status === 'in_progress' && $game->player1_id === auth()->id())
                    <form method="POST" action="{{ route('games.cancel', $game) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Cancel Game
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <!-- Game Result -->
        @if(isset($game) && $game->status === 'completed')
            <div class="mt-6 p-4 bg-gray-50 rounded-lg text-center">
                <h3 class="text-xl font-semibold mb-2">
                    @if($game->winner_id === auth()->id())
                        🎉 You won!
                    @elseif($game->winner_id)
                        Game Over - {{ $game->winner->name }} won!
                    @else
                        It's a draw!
                    @endif
                </h3>
                <p class="text-gray-600 mb-4">
                    The word was: <span class="font-bold text-green-600">{{ $game->word->word }}</span>
                </p>
                <a href="{{ route('games.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Play Again
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gameBoard = document.querySelector('.game-board');
    const wordleForm = document.getElementById('wordle-form');
    const wordleInput = document.getElementById('wordle-input');
    const wordleGrid = document.getElementById('wordle-grid');
    const attemptsCount = document.getElementById('attempts-count');
    
    if (!gameBoard) return;
    
    const gameId = gameBoard.dataset.gameId;
    const maxAttempts = parseInt(gameBoard.dataset.maxAttempts);
    let currentRow = 0;
    let currentCol = 0;
    
    // Initialize game state
    function initializeGame() {
        if (wordleInput) {
            wordleInput.addEventListener('input', function(e) {
                const value = e.target.value.toUpperCase().replace(/[^A-Z]/g, '');
                e.target.value = value;
                
                // Auto-fill tiles
                for (let i = 0; i < 5; i++) {
                    const tile = document.getElementById(`tile-${currentRow}-${i}`);
                    if (tile) {
                        tile.textContent = value[i] || '';
                    }
                }
            });
            
            wordleInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitGuess();
                }
            });
        }
        
        if (wordleForm) {
            wordleForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitGuess();
            });
        }
    }
    
    // Submit guess
    function submitGuess() {
        if (!wordleInput || !gameId) return;
        
        const guess = wordleInput.value.toUpperCase();
        if (guess.length !== 5) {
            alert('Please enter exactly 5 letters');
            return;
        }
        
        // Disable form during submission
        const submitBtn = wordleForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        }
        
        // Send guess to server
        fetch(`/api/games/${gameId}/move`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                guessed_word: guess
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update tiles with colors
                updateTilesWithResult(data.move.result);
                
                // Update attempts count
                if (attemptsCount) {
                    attemptsCount.textContent = data.move.attempt_number;
                }
                
                // Clear input
                wordleInput.value = '';
                
                // Check if game ended
                if (data.game_ended) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    // Move to next row
                    currentRow++;
                    currentCol = 0;
                }
            } else {
                alert(data.error || 'Failed to submit guess');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting your guess');
        })
        .finally(() => {
            // Re-enable form
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Guess';
            }
        });
    }
    
    // Update tiles with result colors
    function updateTilesWithResult(result) {
        for (let i = 0; i < 5; i++) {
            const tile = document.getElementById(`tile-${currentRow}-${i}`);
            if (tile) {
                tile.classList.remove('correct', 'wrong-position', 'incorrect');
                tile.classList.add(result[i]);
                
                // Add animation
                tile.style.animation = 'tileFlip 0.6s ease-in-out';
                setTimeout(() => {
                    tile.style.animation = '';
                }, 600);
            }
        }
    }
    
    // Initialize the game
    initializeGame();
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes tileFlip {
        0% { transform: rotateX(0deg); }
        50% { transform: rotateX(90deg); }
        100% { transform: rotateX(0deg); }
    }
    
    .wordle-tile {
        transition: all 0.3s ease;
    }
    
    .wordle-tile.correct {
        animation: tileFlip 0.6s ease-in-out;
    }
    
    .wordle-tile.wrong-position {
        animation: tileFlip 0.6s ease-in-out;
    }
    
    .wordle-tile.incorrect {
        animation: tileFlip 0.6s ease-in-out;
    }
`;
document.head.appendChild(style);
</script> 