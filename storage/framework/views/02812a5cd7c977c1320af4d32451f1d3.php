<div x-data="{ 
    show: false, 
    title: '', 
    message: '', 
    confirmText: 'Confirm', 
    cancelText: 'Cancel',
    confirmClass: 'bg-red-600 hover:bg-red-700',
    onConfirm: null 
}" 
x-show="show" 
x-transition 
class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" 
x-cloak
x-init="
    window.addEventListener('confirm-action', e => { 
        title = e.detail.title || 'Confirm Action';
        message = e.detail.message || 'Are you sure you want to proceed?';
        confirmText = e.detail.confirmText || 'Confirm';
        cancelText = e.detail.cancelText || 'Cancel';
        confirmClass = e.detail.confirmClass || 'bg-red-600 hover:bg-red-700';
        onConfirm = e.detail.onConfirm;
        show = true; 
    });
    window.addEventListener('close-confirmation', e => { show = false; });
">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all" 
         x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                </div>
            </div>
        </div>
        
        <!-- Body -->
        <div class="px-6 py-4">
            <p class="text-sm text-gray-600" x-text="message"></p>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
            <button 
                @click="show = false" 
                class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200"
                x-text="cancelText">
            </button>
            <button 
                @click="
                    if (onConfirm) onConfirm();
                    show = false;
                " 
                class="px-4 py-2 text-sm font-semibold text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200"
                :class="confirmClass"
                x-text="confirmText">
            </button>
        </div>
    </div>
</div>

<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\components\confirmation-modal.blade.php ENDPATH**/ ?>