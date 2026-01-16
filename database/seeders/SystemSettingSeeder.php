<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'system_name',
                'setting_value' => 'Biometric Attendance System',
                'description' => 'Name of the attendance system',
                'type' => 'string',
                'is_public' => true,
            ],
            [
                'setting_key' => 'institution_name',
                'setting_value' => 'Nasarawa State University, Keffi',
                'description' => 'Name of the institution',
                'type' => 'string',
                'is_public' => true,
            ],
            [
                'setting_key' => 'attendance_threshold',
                'setting_value' => '75',
                'description' => 'Minimum attendance percentage required',
                'type' => 'integer',
                'is_public' => false,
            ],
            [
                'setting_key' => 'face_recognition_enabled',
                'setting_value' => 'true',
                'description' => 'Enable face recognition for attendance',
                'type' => 'boolean',
                'is_public' => false,
            ],
            [
                'setting_key' => 'faceplusplus_api_key',
                'setting_value' => '',
                'description' => 'Face++ API Key for face recognition',
                'type' => 'string',
                'is_public' => false,
            ],
            [
                'setting_key' => 'faceplusplus_api_secret',
                'setting_value' => '',
                'description' => 'Face++ API Secret for face recognition',
                'type' => 'string',
                'is_public' => false,
            ],
            [
                'setting_key' => 'face_confidence_threshold',
                'setting_value' => '75',
                'description' => 'Confidence threshold for face matching (0-100)',
                'type' => 'integer',
                'is_public' => false,
            ],
            // HOD-specific settings
            [
                'setting_key' => 'exam_eligibility_enabled',
                'setting_value' => 'true',
                'description' => 'Enable exam eligibility validation based on attendance',
                'type' => 'boolean',
                'is_public' => false,
            ],
            [
                'setting_key' => 'geofence_radius_km',
                'setting_value' => '0.5',
                'description' => 'Geofence radius in kilometers for attendance verification',
                'type' => 'string',
                'is_public' => false,
            ],
            [
                'setting_key' => 'hod_session_timeout',
                'setting_value' => '3600',
                'description' => 'HOD session timeout in seconds (default: 1 hour)',
                'type' => 'integer',
                'is_public' => false,
            ],
            [
                'setting_key' => 'audit_log_retention_days',
                'setting_value' => '365',
                'description' => 'Number of days to retain audit logs',
                'type' => 'integer',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
