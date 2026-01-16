<!-- Global Modals for the Application -->

<!-- Confirmation Modal -->
<div 
    id="confirmModal" 
    class="fixed inset-0 z-50 overflow-y-auto hidden"
    x-data="{ 
        show: false,
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Yes',
        cancelText: 'No',
        type: 'warning',
        confirmAction: '',
        open() { this.show = true; document.body.classList.add('overflow-hidden'); },
        close() { this.show = false; document.body.classList.remove('overflow-hidden'); },
        confirm() { 
            if (this.confirmAction) {
                eval(this.confirmAction);
            }
            this.close(); 
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm" @click="close()"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-2xl"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Icon based on type -->
                        <div class="flex-shrink-0">
                            <div x-show="type === 'warning'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div x-show="type === 'danger'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div x-show="type === 'info'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div x-show="type === 'success'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold" x-text="title"></h3>
                    </div>
                    <button @click="close()" class="text-white/90 hover:text-white rounded-md p-1 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600" x-text="message"></p>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button 
                    @click="close()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                    <span x-text="cancelText"></span>
                </button>
                <button 
                    @click="confirm()"
                    :class="{
                        'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500': type === 'warning',
                        'bg-red-600 hover:bg-red-700 focus:ring-red-500': type === 'danger',
                        'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': type === 'info',
                        'bg-green-600 hover:bg-green-700 focus:ring-green-500': type === 'success'
                    }"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                >
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div 
    id="alertModal" 
    class="fixed inset-0 z-50 overflow-y-auto hidden"
    x-data="{ 
        show: false,
        title: 'Information',
        message: '',
        confirmText: 'OK',
        type: 'info',
        open() { this.show = true; document.body.classList.add('overflow-hidden'); },
        close() { this.show = false; document.body.classList.remove('overflow-hidden'); },
        confirm() { this.close(); }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm" @click="close()"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-2xl"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Icon based on type -->
                        <div class="flex-shrink-0">
                            <div x-show="type === 'warning'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div x-show="type === 'danger'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div x-show="type === 'info'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div x-show="type === 'success'" class="w-9 h-9 bg-white/15 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold" x-text="title"></h3>
                    </div>
                    <button @click="close()" class="text-white/90 hover:text-white rounded-md p-1 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600" x-text="message"></p>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end">
                <button 
                    @click="confirm()"
                    :class="{
                        'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500': type === 'warning',
                        'bg-red-600 hover:bg-red-700 focus:ring-red-500': type === 'danger',
                        'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': type === 'info',
                        'bg-green-600 hover:bg-green-700 focus:ring-green-500': type === 'success'
                    }"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                >
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global modal functions
window.showModal = function(id, options = {}) {
    const modal = document.getElementById(id);
    if (modal && modal._x_dataStack) {
        const data = modal._x_dataStack[0];
        data.title = options.title || data.title;
        data.message = options.message || data.message;
        data.confirmText = options.confirmText || data.confirmText;
        data.cancelText = options.cancelText || data.cancelText;
        data.type = options.type || data.type;
        data.confirmAction = options.confirmAction || data.confirmAction;
        data.open();
    }
};

window.hideModal = function(id) {
    const modal = document.getElementById(id);
    if (modal && modal._x_dataStack) {
        modal._x_dataStack[0].close();
    }
};

// Enhanced confirm function that returns a Promise
window.confirm = function(message, options = {}) {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        if (modal && modal._x_dataStack) {
            const data = modal._x_dataStack[0];
            data.title = options.title || 'Confirm Action';
            data.message = message;
            data.type = options.type || 'warning';
            data.confirmText = options.confirmText || 'Yes';
            data.cancelText = options.cancelText || 'No';
            data.confirmAction = `window._modalResolve(true);`;
            data.cancelAction = `window._modalResolve(false);`;
            
            // Store resolve function globally
            window._modalResolve = resolve;
            
            data.open();
        }
    });
};

// Enhanced alert function that returns a Promise
window.alert = function(message, options = {}) {
    return new Promise((resolve) => {
        const modal = document.getElementById('alertModal');
        if (modal && modal._x_dataStack) {
            const data = modal._x_dataStack[0];
            data.title = options.title || 'Information';
            data.message = message;
            data.type = options.type || 'info';
            data.confirmText = options.confirmText || 'OK';
            
            // Store resolve function globally
            window._modalResolve = resolve;
            
            data.open();
        }
    });
};

// Utility functions for common modal types
window.showSuccessModal = function(message, title = 'Success') {
    return alert(message, { type: 'success', title });
};

window.showErrorModal = function(message, title = 'Error') {
    return alert(message, { type: 'danger', title });
};

window.showWarningModal = function(message, title = 'Warning') {
    return alert(message, { type: 'warning', title });
};

window.showInfoModal = function(message, title = 'Information') {
    return alert(message, { type: 'info', title });
};

// Confirmation modals with different types
window.confirmDelete = function(message, itemName = 'item') {
    return confirm(message, { 
        type: 'danger', 
        title: 'Delete Confirmation',
        confirmText: 'Delete',
        cancelText: 'Cancel'
    });
};

window.confirmAction = function(message, actionName = 'action') {
    return confirm(message, { 
        type: 'warning', 
        title: 'Confirm Action',
        confirmText: 'Proceed',
        cancelText: 'Cancel'
    });
};
</script>
<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\components\modals.blade.php ENDPATH**/ ?>