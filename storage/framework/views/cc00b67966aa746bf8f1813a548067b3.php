<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'modal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'type' => 'warning', // warning, danger, info, success
    'confirmAction' => '',
    'show' => false
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'id' => 'modal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'type' => 'warning', // warning, danger, info, success
    'confirmAction' => '',
    'show' => false
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div 
    id="<?php echo e($id); ?>" 
    class="fixed inset-0 z-50 overflow-y-auto hidden"
    x-data="{ 
        show: <?php echo \Illuminate\Support\Js::from($show)->toHtml() ?>,
        type: '<?php echo e($type); ?>',
        title: '<?php echo e($title); ?>',
        message: '<?php echo e($message); ?>',
        confirmText: '<?php echo e($confirmText); ?>',
        cancelText: '<?php echo e($cancelText); ?>',
        confirmAction: '<?php echo e($confirmAction); ?>',
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
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="close()"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-md"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <!-- Icon based on type -->
                    <div class="flex-shrink-0">
                        <div x-show="type === 'warning'" class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div x-show="type === 'danger'" class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div x-show="type === 'info'" class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div x-show="type === 'success'" class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                    </div>
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

// Replace confirm() function
window.confirm = function(message) {
    return new Promise((resolve) => {
        showModal('confirmModal', {
            title: 'Confirm Action',
            message: message,
            type: 'warning',
            confirmText: 'Yes',
            cancelText: 'No',
            confirmAction: 'resolve(true)',
            cancelAction: 'resolve(false)'
        });
        
        // Store resolve function globally for modal to use
        window._modalResolve = resolve;
    });
};

// Replace alert() function
window.alert = function(message) {
    return new Promise((resolve) => {
        showModal('alertModal', {
            title: 'Information',
            message: message,
            type: 'info',
            confirmText: 'OK',
            cancelText: '',
            confirmAction: 'resolve(true)'
        });
        
        // Store resolve function globally for modal to use
        window._modalResolve = resolve;
    });
};
</script>
<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\components\modal.blade.php ENDPATH**/ ?>