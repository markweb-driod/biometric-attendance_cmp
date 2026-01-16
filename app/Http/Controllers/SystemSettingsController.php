<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemSettingsController extends Controller
{
    /**
     * Display unified settings page (combines profile settings and system settings)
     */
    public function index()
    {
        $settings = $this->getAllSettings();
        
        // Get face config settings
        $provider = SystemSetting::getValue('face_provider', config('face.provider'));
        $apiKey = SystemSetting::getValue('faceplusplus_api_key', config('face.faceplusplus_api_key'));
        $apiSecret = SystemSetting::getValue('faceplusplus_api_secret', config('face.faceplusplus_api_secret'));
        
        // Get superadmin user for profile
        $user = auth('superadmin')->user();
        
        return view('superadmin.settings', compact('settings', 'provider', 'apiKey', 'apiSecret', 'user'));
    }

    /**
     * Get all system settings organized by category
     */
    private function getAllSettings()
    {
        return Cache::remember('system_settings_all', 3600, function () {
            $allSettings = SystemSetting::all()->pluck('value', 'setting_key');
            
            return [
                'general' => [
                    'institution_name' => $allSettings->get('institution_name', 'NSUK Biometric Attendance System'),
                    'institution_logo' => $allSettings->get('institution_logo', ''),
                    'institution_address' => $allSettings->get('institution_address', ''),
                    'institution_phone' => $allSettings->get('institution_phone', ''),
                    'institution_email' => $allSettings->get('institution_email', ''),
                    'timezone' => $allSettings->get('timezone', 'Africa/Lagos'),
                    'date_format' => $allSettings->get('date_format', 'Y-m-d'),
                    'time_format' => $allSettings->get('time_format', 'H:i:s'),
                ],
                'academic' => [
                    'current_academic_year' => $allSettings->get('current_academic_year', '2024/2025'),
                    'current_semester' => $allSettings->get('current_semester', 'First Semester'),
                    'semester_start_date' => $allSettings->get('semester_start_date', ''),
                    'semester_end_date' => $allSettings->get('semester_end_date', ''),
                    'class_duration_minutes' => $allSettings->get('class_duration_minutes', 60),
                    'attendance_tolerance_minutes' => $allSettings->get('attendance_tolerance_minutes', 15),
                    'max_absence_threshold' => $allSettings->get('max_absence_threshold', 10),
                ],
                'biometric' => [
                    'face_provider' => $allSettings->get('face_provider', 'faceplusplus'),
                    'faceplusplus_api_key' => $allSettings->get('faceplusplus_api_key', ''),
                    'faceplusplus_api_secret' => $allSettings->get('faceplusplus_api_secret', ''),
                    'face_confidence_threshold' => $allSettings->get('face_confidence_threshold', 75),
                    'face_image_quality_threshold' => $allSettings->get('face_image_quality_threshold', 50),
                    'face_detection_sensitivity' => $allSettings->get('face_detection_sensitivity', 'medium'),
                    // Browser AI Settings
                    'enable_browser_face_detection' => $allSettings->get('enable_browser_face_detection', true),
                    'browser_face_confidence_threshold' => $allSettings->get('browser_face_confidence_threshold', 0.5),
                    'browser_face_allow_loose_alignment' => $allSettings->get('browser_face_allow_loose_alignment', true),
                ],
                'security' => [
                    'password_min_length' => $allSettings->get('password_min_length', 8),
                    'password_require_special' => $allSettings->get('password_require_special', true),
                    'password_require_numbers' => $allSettings->get('password_require_numbers', true),
                    'password_require_uppercase' => $allSettings->get('password_require_uppercase', true),
                    'session_timeout_minutes' => $allSettings->get('session_timeout_minutes', 120),
                    'max_login_attempts' => $allSettings->get('max_login_attempts', 5),
                    'lockout_duration_minutes' => $allSettings->get('lockout_duration_minutes', 15),
                    'require_2fa' => $allSettings->get('require_2fa', false),
                ],
                'attendance' => [
                    'enable_gps_verification' => $allSettings->get('enable_gps_verification', true),
                    'gps_accuracy_threshold' => $allSettings->get('gps_accuracy_threshold', 10),
                    'gps_radius_meters' => $allSettings->get('gps_radius_meters', 100),
                    'enable_face_verification' => $allSettings->get('enable_face_verification', true),
                    'enable_manual_attendance' => $allSettings->get('enable_manual_attendance', true),
                    'auto_close_sessions' => $allSettings->get('auto_close_sessions', true),
                    'session_auto_close_minutes' => $allSettings->get('session_auto_close_minutes', 30),
                ],
                'notifications' => [
                    'enable_email_notifications' => $allSettings->get('enable_email_notifications', true),
                    'enable_sms_notifications' => $allSettings->get('enable_sms_notifications', false),
                    'smtp_host' => $allSettings->get('smtp_host', ''),
                    'smtp_port' => $allSettings->get('smtp_port', 587),
                    'smtp_username' => $allSettings->get('smtp_username', ''),
                    'smtp_password' => $allSettings->get('smtp_password', ''),
                    'smtp_encryption' => $allSettings->get('smtp_encryption', 'tls'),
                    'from_email' => $allSettings->get('from_email', ''),
                    'from_name' => $allSettings->get('from_name', 'NSUK Attendance System'),
                ],
                'performance' => [
                    'cache_duration_minutes' => $allSettings->get('cache_duration_minutes', 60),
                    'enable_query_caching' => $allSettings->get('enable_query_caching', true),
                    'max_upload_size_mb' => $allSettings->get('max_upload_size_mb', 10),
                    'image_compression_quality' => $allSettings->get('image_compression_quality', 80),
                    'enable_lazy_loading' => $allSettings->get('enable_lazy_loading', true),
                ],
                'backup' => [
                    'enable_auto_backup' => $allSettings->get('enable_auto_backup', true),
                    'backup_frequency_hours' => $allSettings->get('backup_frequency_hours', 24),
                    'backup_retention_days' => $allSettings->get('backup_retention_days', 30),
                    'backup_location' => $allSettings->get('backup_location', 'local'),
                ]
            ];
        });
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|in:general,academic,biometric,security,attendance,notifications,performance,backup',
            'settings' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = $request->input('category');
        $settings = $request->input('settings');

        try {
            DB::beginTransaction();

            foreach ($settings as $key => $value) {
                SystemSetting::setValue($key, $value);
            }

            // Clear relevant caches
            Cache::forget('system_settings_all');
            Cache::forget('departments_list');
            Cache::forget('academic_levels_list');
            Cache::forget('courses_list');

            // Log the settings update
            Log::info('System settings updated', [
                'category' => $category,
                'updated_by' => auth()->user()->id ?? 'system',
                'settings' => array_keys($settings)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($category) . ' settings updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update system settings', [
                'category' => $category,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'required|string|in:tls,ssl',
            'from_email' => 'required|email',
            'test_email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Configure mail settings temporarily
            config([
                'mail.mailers.smtp.host' => $request->smtp_host,
                'mail.mailers.smtp.port' => $request->smtp_port,
                'mail.mailers.smtp.username' => $request->smtp_username,
                'mail.mailers.smtp.password' => $request->smtp_password,
                'mail.mailers.smtp.encryption' => $request->smtp_encryption,
                'mail.from.address' => $request->from_email,
                'mail.from.name' => 'NSUK Attendance System'
            ]);

            // Send test email
            \Mail::raw('This is a test email from NSUK Biometric Attendance System. If you receive this, your email configuration is working correctly.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - NSUK Attendance System');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->test_email
            ]);

        } catch (\Exception $e) {
            Log::error('Email test failed', [
                'error' => $e->getMessage(),
                'smtp_host' => $request->smtp_host
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Email test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test confidence threshold configuration
     */
    public function testConfidenceThreshold(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'threshold' => 'required|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $threshold = $request->input('threshold');
            
            // Get current API credentials
            $apiKey = SystemSetting::getValue('faceplusplus_api_key', config('face.faceplusplus_api_key'));
            $apiSecret = SystemSetting::getValue('faceplusplus_api_secret', config('face.faceplusplus_api_secret'));
            
            if (!$apiKey || !$apiSecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face++ API credentials not configured'
                ], 400);
            }

            // Simulate confidence testing without API call to prevent timeout
            $testResults = [
                'current_threshold' => $threshold,
                'recommended_thresholds' => [
                    'strict' => 85,
                    'balanced' => 70,
                    'loose' => 55
                ],
                'threshold_analysis' => $this->analyzeThreshold($threshold),
                'api_status' => 'simulated',
                'response_time' => 'N/A (simulated)'
            ];

            return response()->json([
                'success' => true,
                'message' => "Confidence threshold test completed. Current setting: {$threshold}%",
                'data' => $testResults
            ]);

        } catch (\Exception $e) {
            Log::error('Confidence threshold test failed', [
                'error' => $e->getMessage(),
                'threshold' => $request->input('threshold')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Confidence threshold test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze threshold setting and provide recommendations
     */
    private function analyzeThreshold($threshold)
    {
        $analysis = [
            'level' => '',
            'security_rating' => '',
            'usability_rating' => '',
            'recommendations' => []
        ];

        if ($threshold >= 80) {
            $analysis['level'] = 'Very Strict';
            $analysis['security_rating'] = 'High';
            $analysis['usability_rating'] = 'Low';
            $analysis['recommendations'] = [
                'May reject valid faces frequently',
                'Suitable for high-security environments',
                'Consider lowering to 70-75% for better usability'
            ];
        } elseif ($threshold >= 60) {
            $analysis['level'] = 'Balanced';
            $analysis['security_rating'] = 'Good';
            $analysis['usability_rating'] = 'Good';
            $analysis['recommendations'] = [
                'Good balance between security and usability',
                'Recommended for most environments',
                'Monitor false acceptance/rejection rates'
            ];
        } elseif ($threshold >= 40) {
            $analysis['level'] = 'Loose';
            $analysis['security_rating'] = 'Low';
            $analysis['usability_rating'] = 'High';
            $analysis['recommendations'] = [
                'May accept invalid faces',
                'Suitable for low-security environments',
                'Consider increasing to 60-70% for better security'
            ];
        } else {
            $analysis['level'] = 'Very Loose';
            $analysis['security_rating'] = 'Very Low';
            $analysis['usability_rating'] = 'Very High';
            $analysis['recommendations'] = [
                'High risk of false acceptance',
                'Not recommended for secure environments',
                'Strongly recommend increasing to at least 60%'
            ];
        }

        return $analysis;
    }

    /**
     * Test Face++ API configuration
     */
    public function testFaceAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'api_secret' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Use a tiny sample image (1x1 transparent PNG)
            $sampleBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/w8AAn8B9p6Q2wAAAABJRU5ErkJggg==';
            
            $response = \Http::timeout(10)->post('https://api-us.faceplusplus.com/facepp/v3/detect', [
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
                'image_base64' => $sampleBase64,
            ]);

            $result = $response->json();

            if ($response->successful() && !isset($result['error_message'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Face++ API credentials are valid and API is reachable',
                    'response_time' => $response->transferStats->getHandlerStat('total_time') ?? 'N/A'
                ]);
            } else {
                $errorMessage = $result['error_message'] ?? 'Unknown error';
                return response()->json([
                    'success' => false,
                    'message' => 'Face++ API test failed: ' . $errorMessage
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Face++ API test failed', [
                'error' => $e->getMessage(),
                'api_key' => substr($request->api_key, 0, 8) . '...'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Face++ API test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset settings to default values
     */
    public function resetToDefaults(Request $request)
    {
        $category = $request->input('category');
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category is required'
            ], 400);
        }

        $defaults = $this->getDefaultSettings();
        
        if (!isset($defaults[$category])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category'
            ], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($defaults[$category] as $key => $value) {
                SystemSetting::setValue($key, $value);
            }

            Cache::forget('system_settings_all');

            Log::info('System settings reset to defaults', [
                'category' => $category,
                'reset_by' => auth()->user()->id ?? 'system'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($category) . ' settings reset to default values'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get default settings
     */
    private function getDefaultSettings()
    {
        return [
            'general' => [
                'institution_name' => 'NSUK Biometric Attendance System',
                'institution_logo' => '',
                'institution_address' => '',
                'institution_phone' => '',
                'institution_email' => '',
                'timezone' => 'Africa/Lagos',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
            ],
            'academic' => [
                'current_academic_year' => '2024/2025',
                'current_semester' => 'First Semester',
                'semester_start_date' => '',
                'semester_end_date' => '',
                'class_duration_minutes' => 60,
                'attendance_tolerance_minutes' => 15,
                'max_absence_threshold' => 10,
            ],
            'biometric' => [
                'face_provider' => 'faceplusplus',
                'faceplusplus_api_key' => '',
                'faceplusplus_api_secret' => '',
                'face_confidence_threshold' => 75,
                'face_image_quality_threshold' => 50,
                'face_detection_sensitivity' => 'medium',
                'enable_browser_face_detection' => true,
                'browser_face_confidence_threshold' => 0.5,
                'browser_face_allow_loose_alignment' => true,
            ],
            'security' => [
                'password_min_length' => 8,
                'password_require_special' => true,
                'password_require_numbers' => true,
                'password_require_uppercase' => true,
                'session_timeout_minutes' => 120,
                'max_login_attempts' => 5,
                'lockout_duration_minutes' => 15,
                'require_2fa' => false,
            ],
            'attendance' => [
                'enable_gps_verification' => true,
                'gps_accuracy_threshold' => 10,
                'gps_radius_meters' => 100,
                'enable_face_verification' => true,
                'enable_manual_attendance' => true,
                'auto_close_sessions' => true,
                'session_auto_close_minutes' => 30,
            ],
            'notifications' => [
                'enable_email_notifications' => true,
                'enable_sms_notifications' => false,
                'smtp_host' => '',
                'smtp_port' => 587,
                'smtp_username' => '',
                'smtp_password' => '',
                'smtp_encryption' => 'tls',
                'from_email' => '',
                'from_name' => 'NSUK Attendance System',
            ],
            'performance' => [
                'cache_duration_minutes' => 60,
                'enable_query_caching' => true,
                'max_upload_size_mb' => 10,
                'image_compression_quality' => 80,
                'enable_lazy_loading' => true,
            ],
            'backup' => [
                'enable_auto_backup' => true,
                'backup_frequency_hours' => 24,
                'backup_retention_days' => 30,
                'backup_location' => 'local',
            ]
        ];
    }

    /**
     * Export settings configuration
     */
    public function exportSettings()
    {
        $settings = $this->getAllSettings();
        
        $filename = 'system_settings_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Import settings configuration
     */
    public function importSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings_file' => 'required|file|mimes:json|max:1024'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('settings_file');
            $content = file_get_contents($file->getPathname());
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON file'
                ], 400);
            }

            DB::beginTransaction();

            foreach ($settings as $category => $categorySettings) {
                if (is_array($categorySettings)) {
                    foreach ($categorySettings as $key => $value) {
                        SystemSetting::setValue($key, $value);
                    }
                }
            }

            Cache::forget('system_settings_all');

            Log::info('System settings imported', [
                'imported_by' => auth()->user()->id ?? 'system',
                'file_size' => $file->getSize()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settings imported successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to import settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
