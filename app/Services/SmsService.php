<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS notification
     * 
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function sendSms($phoneNumber, $message)
    {
        try {
            // Remove any non-digit characters from phone number
            $phoneNumber = preg_replace('/\D/', '', $phoneNumber);
            
            // Add country code if not present (assuming Nigeria +234)
            if (strlen($phoneNumber) == 10 && substr($phoneNumber, 0, 1) == '0') {
                $phoneNumber = '234' . substr($phoneNumber, 1);
            } elseif (strlen($phoneNumber) == 11 && substr($phoneNumber, 0, 1) == '0') {
                $phoneNumber = '234' . substr($phoneNumber, 1);
            } elseif (strlen($phoneNumber) < 10) {
                return ['success' => false, 'message' => 'Invalid phone number format'];
            }

            // Get SMS provider from config or environment
            $smsProvider = config('services.sms.provider', env('SMS_PROVIDER', 'default'));
            $smsApiKey = config('services.sms.api_key', env('SMS_API_KEY'));
            $smsSenderId = config('services.sms.sender_id', env('SMS_SENDER_ID', 'NSUK'));
            
            // If SMS is disabled or no API key, log and return success (don't fail the process)
            if (!config('services.sms.enabled', env('SMS_ENABLED', false)) || !$smsApiKey) {
                Log::info('SMS notification skipped - SMS disabled or no API key', [
                    'phone' => $phoneNumber,
                    'message' => $message
                ]);
                return ['success' => true, 'message' => 'SMS notification skipped (not configured)'];
            }

            // Example implementation for different SMS providers
            // You can extend this to support multiple providers like Twilio, Nexmo, etc.
            
            // Default implementation using HTTP client
            // Replace with your actual SMS provider API endpoint
            $response = Http::timeout(10)->post(config('services.sms.api_url', env('SMS_API_URL', '')), [
                'api_key' => $smsApiKey,
                'to' => $phoneNumber,
                'from' => $smsSenderId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'provider' => $smsProvider
                ]);
                return ['success' => true, 'message' => 'SMS sent successfully'];
            } else {
                Log::warning('SMS sending failed', [
                    'phone' => $phoneNumber,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return ['success' => false, 'message' => 'Failed to send SMS: ' . $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception - SMS failure shouldn't break face registration
            return ['success' => false, 'message' => 'SMS service error: ' . $e->getMessage()];
        }
    }

    /**
     * Format phone number for SMS
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove spaces, dashes, parentheses
        $phoneNumber = preg_replace('/[\s\-\(\)]/', '', $phoneNumber);
        return $phoneNumber;
    }
}

