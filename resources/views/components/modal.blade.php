<div id="modal-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div id="modal-content" class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Modal Title</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="p-6" id="modal-body">
            <!-- Modal content will be inserted here -->
        </div>
        
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200" id="modal-actions">
            <button type="button" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" onclick="closeModal()">
                Cancel
            </button>
            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" id="modal-confirm">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
// Global modal functions
window.showModal = function(options = {}) {
    const overlay = document.getElementById('modal-overlay');
    const title = document.getElementById('modal-title');
    const body = document.getElementById('modal-body');
    const actions = document.getElementById('modal-actions');
    const confirmBtn = document.getElementById('modal-confirm');
    
    if (!overlay) return;
    
    // Set title
    if (title) {
        title.textContent = options.title || 'Modal Title';
    }
    
    // Set body content
    if (body) {
        body.innerHTML = options.content || '';
    }
    
    // Configure actions
    if (actions && confirmBtn) {
        if (options.showActions === false) {
            actions.classList.add('hidden');
        } else {
            actions.classList.remove('hidden');
            
            // Set confirm button text
            confirmBtn.textContent = options.confirmText || 'Confirm';
            
            // Set confirm button action
            if (options.onConfirm) {
                confirmBtn.onclick = function() {
                    options.onConfirm();
                    closeModal();
                };
            } else {
                confirmBtn.onclick = closeModal;
            }
        }
    }
    
    // Show modal
    overlay.classList.remove('hidden');
    
    // Focus trap
    const focusableElements = overlay.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    if (firstElement) {
        firstElement.focus();
    }
    
    // Handle escape key
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    };
    
    document.addEventListener('keydown', handleEscape);
    
    // Store cleanup function
    overlay.dataset.escapeHandler = handleEscape;
};

window.closeModal = function() {
    const overlay = document.getElementById('modal-overlay');
    
    if (overlay) {
        overlay.classList.add('hidden');
        
        // Remove escape key handler
        const escapeHandler = overlay.dataset.escapeHandler;
        if (escapeHandler) {
            document.removeEventListener('keydown', escapeHandler);
            delete overlay.dataset.escapeHandler;
        }
    }
};

// Close modal when clicking overlay
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('modal-overlay');
    
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeModal();
            }
        });
    }
});

// Confirmation modal helper
window.showConfirmation = function(message, onConfirm, title = 'Confirm Action') {
    showModal({
        title: title,
        content: `<p class="text-gray-700">${message}</p>`,
        confirmText: 'Confirm',
        onConfirm: onConfirm
    });
};

// Alert modal helper
window.showAlert = function(message, title = 'Alert') {
    showModal({
        title: title,
        content: `<p class="text-gray-700">${message}</p>`,
        confirmText: 'OK',
        onConfirm: null
    });
};
</script> 