<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-4"></div>
        <p class="text-gray-700 font-medium" id="loading-message">Loading...</p>
    </div>
</div>

<script>
// Global loading functions
window.showLoading = function(message = 'Loading...') {
    const overlay = document.getElementById('loading-overlay');
    const messageEl = document.getElementById('loading-message');
    
    if (overlay && messageEl) {
        messageEl.textContent = message;
        overlay.classList.remove('hidden');
    }
};

window.hideLoading = function() {
    const overlay = document.getElementById('loading-overlay');
    
    if (overlay) {
        overlay.classList.add('hidden');
    }
};

// Auto-hide loading after form submissions
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            // Don't show loading for game moves (handled separately)
            if (!form.id || form.id !== 'wordle-form') {
                window.showLoading('Processing...');
            }
        });
    });
    
    // Hide loading when page loads
    window.hideLoading();
});
</script> 