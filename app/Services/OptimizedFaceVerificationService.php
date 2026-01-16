<?php

namespace App\Services;

use App\Models\Student;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OptimizedFaceVerificationService
{
    private $apiKey;
    private $apiSecret;
    private $confidenceThreshold;

    public function __construct()
    {
        $this->apiKey = SystemSetting::getValue('faceplusplus_api_key', config('face.faceplusplus_api_key'));
        $this->apiSecret = SystemSetting::getValue('faceplusplus_api_secret', config('face.faceplusplus_api_secret'));
        $this->confidenceThreshold = SystemSetting::getValue('face_confidence_threshold', 75);
    }

    /**
     * Optimized face verification with caching and timeout
     */
    public function verifyFace($studentId, $liveImage)
    {
        // Check if face verification is enabled
        if (!SystemSetting::getValue('enable_face_verification', true)) {
            return [
                'success' => true,
                'confidence' => 100,
                'message' => 'Face verification disabled'
            ];
        }

        try {
            $student = Student::select(['id', 'reference_image_path'])
                ->find($studentId);

            if (!$student || !$student->reference_image_path) {
                return [
                    'success' => false,
                    'message' => 'No reference image found'
                ];
            }

            // Check cache first
            $cacheKey = "face_verification_{$studentId}_" . md5($liveImage);
            $cachedResult = Cache::get($cacheKey);
            
            if ($cachedResult) {
                return $cachedResult;
            }

            // Perform verification with timeout
            $result = $this->performFaceVerification($student, $liveImage);
            
            // Cache result for 5 minutes
            Cache::put($cacheKey, $result, 300);
            
            return $result;

        } catch (\Exception $e) {
            \Log::error('Face verification error', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Face verification failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Perform actual face verification with timeout
     */
    private function performFaceVerification($student, $liveImage)
    {
        // Load reference image
        $referenceImagePath = storage_path('app/public/' . $student->reference_image_path);
        
        if (!file_exists($referenceImagePath)) {
            return [
                'success' => false,
                'message' => 'Reference image file not found'
            ];
        }

        $referenceBase64 = base64_encode(file_get_contents($referenceImagePath));

        // Make API call with timeout
        $response = Http::timeout(10) // 10 second timeout
            ->retry(2, 1000) // Retry twice with 1 second delay
            ->asForm()
            ->post('https://api-us.faceplusplus.com/facepp/v3/compare', [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'image_base64_1' => $referenceBase64,
                'image_base64_2' => $liveImage,
            ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => 'Face verification service unavailable'
            ];
        }

        $result = $response->json();

        if (isset($result['error_message'])) {
            return [
                'success' => false,
                'message' => 'Face verification error: ' . $result['error_message']
            ];
        }

        $confidence = $result['confidence'] ?? 0;
        $isMatch = $confidence > $this->confidenceThreshold;

        return [
            'success' => $isMatch,
            'confidence' => $confidence,
            'threshold' => $this->confidenceThreshold,
            'message' => $isMatch ? 'Face verification successful' : 'Face verification failed'
        ];
    }

    /**
     * Batch face verification for multiple students
     */
    public function batchVerifyFaces($verifications)
    {
        $results = [];
        
        foreach ($verifications as $verification) {
            $results[] = $this->verifyFace(
                $verification['student_id'],
                $verification['image']
            );
        }
        
        return $results;
    }

    /**
     * Test face verification service
     */
    public function testService()
    {
        try {
            // Use a small test image
            $testImage = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/w8AAn8B9p6Q2wAAAABJRU5ErkJggg==';
            
            $response = Http::timeout(5)
                ->asForm()
                ->post('https://api-us.faceplusplus.com/facepp/v3/detect', [
                    'api_key' => $this->apiKey,
                    'api_secret' => $this->apiSecret,
                    'image_base64' => $testImage,
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->successful() ? 'Service is operational' : 'Service is down'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Service test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get verification statistics
     */
    public function getVerificationStats()
    {
        return Cache::remember('face_verification_stats', 600, function () {
            // Get actual verification statistics from attendance records
            $totalVerifications = \App\Models\Attendance::count();
            $successfulVerifications = \App\Models\Attendance::where('status', 'present')->count();
            
            $successRate = $totalVerifications > 0 ? 
                round(($successfulVerifications / $totalVerifications) * 100, 1) : 0;
            
            // Calculate average confidence from recent verifications
            $recentAttendances = \App\Models\Attendance::where('created_at', '>=', now()->subDays(7))->get();
            $averageConfidence = $recentAttendances->avg('confidence_score') ?? 0;
            
            return [
                'total_verifications' => $totalVerifications,
                'success_rate' => $successRate,
                'average_confidence' => round($averageConfidence, 1),
                'service_status' => 'operational'
            ];
        });
    }
}
