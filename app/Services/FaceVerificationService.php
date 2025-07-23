<?php

namespace App\Services;

use App\Models\Student;
use App\Models\SystemSetting;

class FaceVerificationService
{
    public function verifyFace($studentId, $liveImage)
    {
        $provider = config('face.provider', 'faceplusplus');
        if ($provider === 'faceplusplus') {
            return (new FacePlusPlusProvider())->verifyFace($studentId, $liveImage);
        }
        // Add more providers as needed
        throw new \Exception('No face provider configured.');
    }
}

class FacePlusPlusProvider
{
    public function verifyFace($studentId, $liveImage)
    {
        $student = Student::findOrFail($studentId);
        $referenceImage = $student->reference_image_path; // Path or base64
        // Load reference image as base64
        $referenceBase64 = base64_encode(file_get_contents(storage_path('app/public/' . $referenceImage)));
        // $liveImage is expected as base64 string
        $apiKey = SystemSetting::getValue('faceplusplus_api_key', config('face.faceplusplus_api_key'));
        $apiSecret = SystemSetting::getValue('faceplusplus_api_secret', config('face.faceplusplus_api_secret'));
        $response = \Http::asForm()->post('https://api-us.faceplusplus.com/facepp/v3/compare', [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'image_base64_1' => $referenceBase64,
            'image_base64_2' => $liveImage,
        ]);
        $result = $response->json();
        // Face++ returns 'confidence' (0-100), and thresholds for 1e-3, 1e-4, 1e-5 FAR
        $isMatch = isset($result['confidence']) && $result['confidence'] > 75; // Tune threshold as needed
        return [
            'success' => $isMatch,
            'confidence' => $result['confidence'] ?? 0,
            'raw' => $result,
        ];
    }
} 