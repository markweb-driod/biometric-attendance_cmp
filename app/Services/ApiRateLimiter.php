<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ApiRateLimiter
{
    /**
     * Check if API key can make a request based on rate limits
     */
    public function checkLimit(ApiKey $apiKey): array
    {
        $perMinuteKey = "api_rate_limit:{$apiKey->id}:minute:" . Carbon::now()->format('Y-m-d-H-i');
        $perHourKey = "api_rate_limit:{$apiKey->id}:hour:" . Carbon::now()->format('Y-m-d-H');
        
        $minuteCount = Cache::get($perMinuteKey, 0);
        $hourCount = Cache::get($perHourKey, 0);
        
        $canMakeRequest = true;
        $reason = null;
        $retryAfter = null;
        
        // Check per-minute limit
        if ($minuteCount >= $apiKey->rate_limit_per_minute) {
            $canMakeRequest = false;
            $reason = 'minute_limit_exceeded';
            $retryAfter = 60 - Carbon::now()->second; // seconds until next minute
        }
        
        // Check per-hour limit
        if ($hourCount >= $apiKey->rate_limit_per_hour) {
            $canMakeRequest = false;
            $reason = 'hour_limit_exceeded';
            $nextHour = Carbon::now()->startOfHour()->addHour();
            $retryAfter = $nextHour->diffInSeconds(Carbon::now());
        }
        
        return [
            'allowed' => $canMakeRequest,
            'reason' => $reason,
            'retry_after' => $retryAfter,
            'current_minute' => $minuteCount,
            'current_hour' => $hourCount,
            'limit_minute' => $apiKey->rate_limit_per_minute,
            'limit_hour' => $apiKey->rate_limit_per_hour,
        ];
    }

    /**
     * Record an API request (increment counters)
     */
    public function recordRequest(ApiKey $apiKey): void
    {
        $perMinuteKey = "api_rate_limit:{$apiKey->id}:minute:" . Carbon::now()->format('Y-m-d-H-i');
        $perHourKey = "api_rate_limit:{$apiKey->id}:hour:" . Carbon::now()->format('Y-m-d-H');
        
        // Increment counters
        Cache::increment($perMinuteKey);
        Cache::increment($perHourKey);
        
        // Set expiration for cache keys
        Cache::put($perMinuteKey, Cache::get($perMinuteKey), Carbon::now()->addMinute());
        Cache::put($perHourKey, Cache::get($perHourKey), Carbon::now()->addHour());
    }
}

