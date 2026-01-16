<?php

use App\Models\Hod;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing HOD Model Access...\n";

try {
    $hod = Hod::with(['user', 'department'])->where('staff_id', 'HOD001')->first();

    if ($hod) {
        echo "HOD Found: " . $hod->staff_id . "\n";
        echo "User: " . ($hod->user ? $hod->user->name : 'No User') . "\n";
        echo "Department: " . ($hod->department ? $hod->department->name : 'No Department') . "\n";
    } else {
        echo "HOD Not Found.\n";
    }
} catch (\Exception $e) {
    echo "Exception Caught: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} catch (\Throwable $t) {
    echo "Fatal Error Caught: " . $t->getMessage() . "\n";
    echo $t->getTraceAsString();
}
