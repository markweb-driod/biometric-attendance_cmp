
<?php $__env->startSection('title', 'Setup Two-Factor Authentication'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-md mx-auto p-6 mt-8 bg-white rounded-lg shadow border border-blue-100">
    <h1 class="text-xl font-bold mb-4">Setup Two-Factor Authentication</h1>
    
    <div class="mb-4">
        <p class="text-sm text-gray-700 mb-4">
            Scan the QR code below with your authenticator app (Google Authenticator, Authy, etc.) to enable two-factor authentication.
        </p>
        
        <div class="flex justify-center mb-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo e(urlencode($qrCodeUrl)); ?>" alt="QR Code" class="border border-gray-300 p-2">
        </div>
        
        <div class="mb-4 p-3 bg-gray-50 rounded border">
            <p class="text-xs text-gray-600 mb-2">Can't scan? Enter this code manually:</p>
            <p class="font-mono text-sm text-center"><?php echo e(chunk_split($secret, 4, ' ')); ?></p>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('hod.two-factor.confirm')); ?>">
        <?php echo csrf_field(); ?>
        <label for="two_factor_code" class="block text-sm font-semibold mb-1">Enter 6-digit code from your app</label>
        <input name="two_factor_code" id="two_factor_code" type="text" class="w-full border px-3 py-2 rounded mb-2" maxlength="6" autofocus required inputmode="numeric" pattern="[0-9]{6}">
        <?php $__errorArgs = ['two_factor_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="text-red-700 text-xs mb-2"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Enable Two-Factor Authentication
        </button>
    </form>
    
    <div class="mt-4">
        <a href="<?php echo e(route('hod.dashboard')); ?>" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.hod', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\two-factor\setup.blade.php ENDPATH**/ ?>