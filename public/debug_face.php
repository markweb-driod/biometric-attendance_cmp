<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

function d($label, $data) {
    echo "<strong>$label:</strong><br>";
    if (is_array($data) || is_object($data)) {
        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo $data . "<br>";
    }
    echo "<hr>";
}

echo "<h1>Deep Config Debug</h1>";

// 1. Direct File Read
$envContent = file_get_contents(__DIR__ . '/../.env');
// Check raw content for key
$hasKeyInFile = strpos($envContent, 'FACEPLUSPLUS_API_KEY=') !== false;
d(".env File has Key?", $hasKeyInFile ? "YES" : "NO");

// 2. Env function
d("env('FACEPLUSPLUS_API_KEY')", env('FACEPLUSPLUS_API_KEY', 'DEFAULT_VAL'));

// 3. Config function
d("config('face')", config('face'));

// 4. Config file check
$configFile = include __DIR__ . '/../config/face.php';
d("Direct include config/face.php", $configFile);
