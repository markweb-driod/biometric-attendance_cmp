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
        try {
            $student = Student::findOrFail($studentId);
            $referenceImage = $student->reference_image_path; // Path or base64
            
            if (!$referenceImage) {
                return [
                    'success' => false,
                    'confidence' => 0,
                    'raw' => ['error' => 'No reference image found'],
                    'error' => 'No reference image found for student'
                ];
            }

            // Check if reference image file exists
            $referenceImagePath = storage_path('app/public/' . $referenceImage);
            if (!file_exists($referenceImagePath)) {
                return [
                    'success' => false,
                    'confidence' => 0,
                    'raw' => ['error' => 'Reference image file not found'],
                    'error' => 'Reference image file not found'
                ];
            }

            // Load reference image as base64
            $referenceBase64 = base64_encode(file_get_contents($referenceImagePath));
            
            // Get API credentials
            $apiKey = SystemSetting::getValue('faceplusplus_api_key', config('face.faceplusplus_api_key'));
            $apiSecret = SystemSetting::getValue('faceplusplus_api_secret', config('face.faceplusplus_api_secret'));
            
            if (!$apiKey || !$apiSecret) {
                return [
                    'success' => false,
                    'confidence' => 0,
                    'raw' => ['error' => 'API credentials not configured'],
                    'error' => 'Face++ API credentials not configured'
                ];
            }

            // Make API call to Face++
            // NOTE: Live image comes as data:image/jpeg;base64,... format, need to extract just the base64 part
            $liveImageBase64 = $liveImage;
            if (preg_match('/^data:image\/(\w+);base64,/', $liveImage, $matches)) {
                // Remove data URL prefix
                $liveImageBase64 = substr($liveImage, strpos($liveImage, ',') + 1);
            }
            
            // Both images should be pure base64 without data URL prefix
            $response = \Http::timeout(30)->asForm()->post('https://api-us.faceplusplus.com/facepp/v3/compare', [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'image_base64_1' => $referenceBase64,
                'image_base64_2' => $liveImageBase64,
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'confidence' => 0,
                    'raw' => ['error' => 'API request failed', 'status' => $response->status()],
                    'error' => 'Face++ API request failed: ' . $response->status()
                ];
            }

            $result = $response->json();
            
            // Log the API response for debugging
            \Log::info('Face verification API response', [
                'student_id' => $studentId,
                'api_response' => $result
            ]);

            // Get dynamic confidence threshold from system settings
            $confidenceThreshold = SystemSetting::getValue('face_confidence_threshold', 75);
            
            // Face++ returns 'confidence' (0-100), and thresholds for 1e-3, 1e-4, 1e-5 FAR
            $confidence = $result['confidence'] ?? 0;
            $isMatch = $confidence > $confidenceThreshold;
            
            return [
                'success' => $isMatch,
                'confidence' => $confidence,
                'raw' => $result,
            ];

        } catch (\Exception $e) {
            \Log::error('Face verification failed', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'confidence' => 0,
                'raw' => ['error' => $e->getMessage()],
                'error' => $e->getMessage()
            ];
        }
    }
} 