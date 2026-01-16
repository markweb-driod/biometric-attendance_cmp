<?php

use App\Models\Hod;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$staffId = 'HOD001';
$newPassword = 'password123';
$email = 'hod@example.com';

echo "Searching for HOD with Staff ID: $staffId...\n";

$hod = Hod::where('staff_id', $staffId)->first();

if (!$hod) {
    echo "HOD not found! Creating one...\n";
    // Check if user exists
    $user = User::where('email', $email)->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Dr. John Smith',
            'email' => $email,
            'password' => Hash::make($newPassword),
            'role' => 'hod', // Assuming role column exists or handled via relationship
        ]);
        echo "Created generic User record.\n";
    }

    $hod = Hod::create([
        'user_id' => $user->id,
        'department_id' => 1, // Approximating department ID 1
        'staff_id' => $staffId,
        'title' => 'Dr.',
        'is_active' => true,
    ]);
    echo "Created HOD record.\n";
} else {
    echo "HOD found. Updating password...\n";
    if ($hod->user) {
        $hod->user->password = Hash::make($newPassword);
        $hod->user->save();
        echo "Password updated to '$newPassword'.\n";
    } else {
        echo "Error: HOD record has no associated User record.\n";
        // Create user and link
        $user = User::create([
            'name' => 'Dr. John Smith',
            'email' => $email,
            'password' => Hash::make($newPassword),
        ]);
        $hod->user_id = $user->id;
        $hod->save();
        echo "Created and linked new User record.\n";
    }
}

// Ensure active
$hod->is_active = true;
$hod->save();

echo "Done. HOD Details:\n";
echo "Staff ID: " . $hod->staff_id . "\n";
echo "Password: " . $newPassword . "\n";
echo "Login URL: " . url('/login') . "\n";
