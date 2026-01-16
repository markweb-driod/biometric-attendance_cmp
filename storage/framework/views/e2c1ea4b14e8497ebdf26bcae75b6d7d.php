<div x-data="{ show: false, message: '', type: 'success' }" x-show="show" x-transition class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded shadow-lg text-white text-sm font-semibold"
     :class="type === 'success' ? 'bg-[#008000]' : 'bg-red-600'" x-cloak
     x-init="window.addEventListener('toast', e => { message = e.detail.message; type = e.detail.type; show = true; setTimeout(() => show = false, 3000); })">
    <span x-text="message"></span>
</div> <?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\components\toast.blade.php ENDPATH**/ ?>