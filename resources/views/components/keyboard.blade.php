<div class="virtual-keyboard mt-6">
    <div class="max-w-md mx-auto">
        <!-- First row: Q W E R T Y U I O P -->
        <div class="flex justify-center mb-2">
            @foreach(['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'] as $key)
                <button class="keyboard-key" data-key="{{ $key }}">
                    {{ $key }}
                </button>
            @endforeach
        </div>
        
        <!-- Second row: A S D F G H J K L -->
        <div class="flex justify-center mb-2">
            <div class="flex space-x-1">
                @foreach(['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'] as $key)
                    <button class="keyboard-key" data-key="{{ $key }}">
                        {{ $key }}
                    </button>
                @endforeach
            </div>
        </div>
        
        <!-- Third row: Z X C V B N M + Enter -->
        <div class="flex justify-center">
            <div class="flex space-x-1">
                <button class="keyboard-key px-4" data-key="ENTER">
                    Enter
                </button>
                @foreach(['Z', 'X', 'C', 'V', 'B', 'N', 'M'] as $key)
                    <button class="keyboard-key" data-key="{{ $key }}">
                        {{ $key }}
                    </button>
                @endforeach
                <button class="keyboard-key px-4" data-key="BACKSPACE">
                    ←
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const keyboard = document.querySelector('.virtual-keyboard');
    const wordleInput = document.getElementById('wordle-input');
    
    if (!keyboard || !wordleInput) return;
    
    // Handle keyboard clicks
    keyboard.addEventListener('click', function(e) {
        if (e.target.classList.contains('keyboard-key')) {
            const key = e.target.dataset.key;
            
            if (key === 'ENTER') {
                // Submit the form
                const form = document.getElementById('wordle-form');
                if (form) {
                    form.dispatchEvent(new Event('submit'));
                }
            } else if (key === 'BACKSPACE') {
                // Remove last character
                const currentValue = wordleInput.value;
                wordleInput.value = currentValue.slice(0, -1);
                wordleInput.dispatchEvent(new Event('input'));
            } else {
                // Add character if we have space
                const currentValue = wordleInput.value;
                if (currentValue.length < 5) {
                    wordleInput.value = currentValue + key;
                    wordleInput.dispatchEvent(new Event('input'));
                }
            }
            
            // Focus back to input
            wordleInput.focus();
        }
    });
    
    // Update keyboard colors based on game state
    function updateKeyboardColors(gameState) {
        if (!gameState || !gameState.moves) return;
        
        const keyStates = {};
        
        // Process all moves to determine key states
        gameState.moves.forEach(move => {
            const word = move.guessed_word;
            const result = move.result;
            
            for (let i = 0; i < 5; i++) {
                const letter = word[i];
                const letterResult = result[i];
                
                // Only update if this is a better state (correct > wrong_position > incorrect)
                if (!keyStates[letter] || getStatePriority(letterResult) > getStatePriority(keyStates[letter])) {
                    keyStates[letter] = letterResult;
                }
            }
        });
        
        // Apply colors to keyboard keys
        Object.keys(keyStates).forEach(letter => {
            const keyElement = keyboard.querySelector(`[data-key="${letter}"]`);
            if (keyElement) {
                // Remove existing classes
                keyElement.classList.remove('correct', 'wrong-position', 'incorrect');
                
                // Add new class
                if (keyStates[letter] !== 'incorrect') {
                    keyElement.classList.add(keyStates[letter]);
                }
            }
        });
    }
    
    // Helper function to get state priority
    function getStatePriority(state) {
        switch (state) {
            case 'correct': return 3;
            case 'wrong_position': return 2;
            case 'incorrect': return 1;
            default: return 0;
        }
    }
    
    // Expose update function globally
    window.updateVirtualKeyboard = updateKeyboardColors;
});
</script>

<style>
.virtual-keyboard {
    user-select: none;
}

.keyboard-key {
    min-width: 40px;
    height: 50px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
}

.keyboard-key:hover {
    transform: scale(1.05);
}

.keyboard-key:active {
    transform: scale(0.95);
}

.keyboard-key.correct {
    background-color: #10b981;
    color: white;
}

.keyboard-key.wrong-position {
    background-color: #f59e0b;
    color: white;
}

.keyboard-key.incorrect {
    background-color: #6b7280;
    color: white;
}

@media (max-width: 640px) {
    .keyboard-key {
        min-width: 32px;
        height: 45px;
        font-size: 12px;
        margin: 1px;
    }
}
</style> 