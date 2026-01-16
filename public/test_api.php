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

echo "<h1>Face API Connectivity Test</h1>";

try {
    $student = \App\Models\Student::where('is_active', true)->whereNotNull('reference_image_path')->first();
    if (!$student) die("No active student with reference image.");

    $fullPath = storage_path('app/public/' . $student->reference_image_path);
    if (!file_exists($fullPath)) die("Ref image missing: $fullPath");

    $refContent = file_get_contents($fullPath);
    $base64 = base64_encode($refContent);
    // Simulate live image
    $liveImage = $base64; 

    d("Testing with Student", $student->id);
    d("API Key Status", config('face.faceplusplus_api_key') ? "LOADED" : "MISSING");

    $service = new \App\Services\FaceVerificationService();
    $result = $service->verifyFace($student->id, $liveImage); // Note: Service expects base64 or data-uri

    d("Final Result", $result);

} catch (\Exception $e) {
    d("EXCEPTION", $e->getMessage());
}
