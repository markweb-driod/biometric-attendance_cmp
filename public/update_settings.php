<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\SystemSetting;

$k = 'rX82_Rb07t4IbeSjxZ8p4JLFOhivEAHj';
$s = '5KAGtOvZo4ri9hdZ-OtR_0IP3muQzkn1';

SystemSetting::updateOrCreate(
    ['setting_key' => 'faceplusplus_api_key'], 
    ['setting_value' => (string)$k, 'type' => 'string']
);

SystemSetting::updateOrCreate(
    ['setting_key' => 'faceplusplus_api_secret'], 
    ['setting_value' => (string)$s, 'type' => 'string']
);

// Ensure Face Verification is ENABLED
SystemSetting::updateOrCreate(
    ['setting_key' => 'enable_face_verification'], 
    ['setting_value' => '1', 'type' => 'boolean']
);

echo "Settings Updated in DB.";
