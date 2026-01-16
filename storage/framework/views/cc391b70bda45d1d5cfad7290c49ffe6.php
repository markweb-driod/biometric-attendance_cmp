
<?php $__env->startSection('title', 'Setup Two-Factor Authentication'); ?>
<?php $__env->startSection('content'); ?>
<!-- Flash Messages -->
<?php if(session('success')): ?>
<div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span><?php echo e(session('success')); ?></span>
    <button onclick="closeFlash('flash-success')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span><?php echo e(session('error')); ?></span>
    <button onclick="closeFlash('flash-error')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

<div class="max-w-md mx-auto p-6 mt-8 bg-white rounded-lg shadow border border-purple-100">
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

    <form method="POST" action="<?php echo e(route('lecturer.2fa.confirm')); ?>">
        <?php echo csrf_field(); ?>
        <label for="code" class="block text-sm font-semibold mb-1">Enter 6-digit code from your app</label>
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
        <button type="submit" class="w-full py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Enable Two-Factor Authentication
        </button>
    </form>
    
    <div class="mt-4">
        <a href="<?php echo e(route('lecturer.dashboard')); ?>" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\two-factor\setup.blade.php ENDPATH**/ ?>