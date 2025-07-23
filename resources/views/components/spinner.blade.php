<div x-data="{ show: false }" x-show="show" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30" x-cloak
     x-init="window.addEventListener('spinner', e => { show = e.detail.show; })">
    <div class="w-16 h-16 border-4 border-[#008000] border-t-transparent rounded-full animate-spin"></div>
</div> 