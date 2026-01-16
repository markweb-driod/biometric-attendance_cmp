<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING STUDENT MODEL ===\n\n";

$student = new \App\Models\Student();
echo "Fillable fields:\n";
print_r($student->getFillable());

echo "\nChecking if reference_image_path exists in fillable...\n";
if (in_array('reference_image_path', $student->getFillable())) {
    echo "✅ reference_image_path is fillable\n";
} else {
    echo "❌ reference_image_path is not fillable\n";
}

echo "\nChecking if face_registration_enabled exists in fillable...\n";
if (in_array('face_registration_enabled', $student->getFillable())) {
    echo "✅ face_registration_enabled is fillable\n";
} else {
    echo "❌ face_registration_enabled is not fillable\n";
}

// Check a sample student
$sampleStudent = \App\Models\Student::first();
if ($sampleStudent) {
    echo "\nSample student data:\n";
    echo "ID: " . $sampleStudent->id . "\n";
    echo "Matric: " . $sampleStudent->matric_number . "\n";
    echo "Reference Image Path: " . ($sampleStudent->reference_image_path ?? 'NULL') . "\n";
    echo "Face Registration Enabled: " . ($sampleStudent->face_registration_enabled ? 'Yes' : 'No') . "\n";
} else {
    echo "\nNo students found in database\n";
}
