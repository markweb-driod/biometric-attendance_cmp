<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Superadmin Login'); ?> - NSUK Biometric Attendance</title>
    <meta name="description" content="Superadmin authentication for NSUK Biometric Attendance">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <!-- Local fonts -->
    <link href="/fonts/montserrat/montserrat.css" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>body { font-family: 'Montserrat', sans-serif; }</style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-gray-100 min-h-screen flex flex-col">
    <?php echo $__env->yieldContent('content'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\layouts\superadmin_auth.blade.php ENDPATH**/ ?>