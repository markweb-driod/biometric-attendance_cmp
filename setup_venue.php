<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$v = App\Models\Venue::firstOrCreate(
    ['name' => 'Test Venue'],
    ['latitude' => 0, 'longitude' => 0, 'radius' => 100, 'is_active' => true]
);

$s = App\Models\AttendanceSession::where('code', 'WEUNJO')->first();
if ($s) {
    $s->venue_id = $v->id;
    $s->save();
    echo "Venue ID set to: " . $s->venue_id . "\n";
} else {
    echo "Session WEUNJO not found.\n";
}
