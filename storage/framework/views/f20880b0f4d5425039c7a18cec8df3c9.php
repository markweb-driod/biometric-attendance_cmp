
<?php $__env->startSection('title', 'Two-Factor Authentication'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-sm mx-auto p-5 mt-16 bg-white rounded-lg shadow border border-green-100"
     x-data="{ isLoading: false }"
     x-init="
         <?php if(session('success')): ?>
            setTimeout(() => { window.showSuccessModal ? window.showSuccessModal('<?php echo e(session('success')); ?>') : alert('<?php echo e(session('success')); ?>'); }, 300);
         <?php endif; ?>
         <?php if(session('error')): ?>
            setTimeout(() => { window.showErrorModal ? window.showErrorModal('<?php echo e(session('error')); ?>') : alert('<?php echo e(session('error')); ?>'); }, 300);
         <?php endif; ?>
         <?php if($errors->has('code')): ?>
            setTimeout(() => { window.showErrorModal ? window.showErrorModal('<?php echo e($errors->first('code')); ?>', 'Two-Factor Authentication') : alert('<?php echo e($errors->first('code')); ?>') }, 300);
         <?php endif; ?>
     "
>
    <h1 class="text-lg font-bold mb-4">Two-Factor Authentication</h1>
    <form method="POST" action="<?php echo e(route('superadmin.2fa.verify')); ?>"
          @submit.prevent="isLoading = true; $el.submit();">
        <?php echo csrf_field(); ?>
        <label for="code" class="block text-sm font-semibold mb-1">Enter 6-digit code</label>
        <input name="code" id="code" type="text" class="w-full border px-3 py-2 rounded mb-2" maxlength="6" autofocus required inputmode="numeric" pattern="[0-9]{6}">
        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="text-red-700 text-xs mb-2"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <button type="submit" class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700 flex justify-center items-center disabled:opacity-60" :disabled="isLoading">
            <template x-if="isLoading">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
            </template>
            <span x-text="isLoading ? 'Verifying...' : 'Verify'"></span>
        </button>
    </form>
    <noscript>
        <?php if(session('success')): ?><div class="mt-4 p-2 bg-green-100 border border-green-200 rounded text-green-900"><?php echo e(session('success')); ?></div><?php endif; ?>
        <?php if(session('error')): ?><div class="mt-4 p-2 bg-red-100 border border-red-200 rounded text-red-900"><?php echo e(session('error')); ?></div><?php endif; ?>
    </noscript>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\2fa.blade.php ENDPATH**/ ?>