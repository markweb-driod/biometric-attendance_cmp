<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;
use App\Services\ApiRateLimiter;

class AuthenticateApiKey
{
    protected $rateLimiter;

    public function __construct(ApiRateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key from header or query parameter
        $apiKeyValue = $request->header('X-API-Key') ?? $request->query('api_key');
        
        if (!$apiKeyValue) {
            return response()->json([
                'success' => false,
                'error' => 'missing_api_key',
                'message' => 'API key is required. Provide it via X-API-Key header or api_key query parameter.',
            ], 401);
        }

        // Find API key
        $apiKey = ApiKey::findByKey($apiKeyValue);
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'invalid_api_key',
                'message' => 'Invalid API key provided.',
            ], 401);
        }

        // Check if key is active
        if (!$apiKey->isActive()) {
            return response()->json([
                'success' => false,
                'error' => 'api_key_inactive',
                'message' => 'API key is not active or has expired.',
            ], 403);
        }

        // Check rate limits
        $rateLimitCheck = $this->rateLimiter->checkLimit($apiKey);
        
        if (!$rateLimitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'error' => 'rate_limit_exceeded',
                'message' => 'Rate limit exceeded. Please try again later.',
                'limit_type' => $rateLimitCheck['reason'],
                'retry_after' => $rateLimitCheck['retry_after'],
                'current_usage' => [
                    'minute' => $rateLimitCheck['current_minute'],
                    'hour' => $rateLimitCheck['current_hour'],
                ],
                'limits' => [
                    'per_minute' => $rateLimitCheck['limit_minute'],
                    'per_hour' => $rateLimitCheck['limit_hour'],
                ],
            ], 429);
        }

        // Record request for rate limiting
        $this->rateLimiter->recordRequest($apiKey);

        // Set API key in request for controllers to use
        $request->merge(['api_key_model' => $apiKey]);
        $request->attributes->set('api_key', $apiKey);

        // Continue with request
        $response = $next($request);

        // Log the request after response is generated
        $this->logRequest($apiKey, $request, $response);

        return $response;
    }

    /**
     * Log API request
     */
    protected function logRequest(ApiKey $apiKey, Request $request, Response $response): void
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $responseTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        $apiKey->logRequest([
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'response_status' => $response->getStatusCode(),
            'response_time_ms' => round($responseTime, 2),
            'request_payload' => $this->sanitizePayload($request->all()),
            'error_message' => $response->getStatusCode() >= 400 ? $this->extractErrorMessage($response) : null,
        ]);
    }

    /**
     * Sanitize request payload for logging (remove sensitive data)
     */
    protected function sanitizePayload(array $payload): array
    {
        $sensitiveKeys = ['password', 'api_key', 'token', 'secret', 'authorization'];
        
        foreach ($payload as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $payload[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $payload[$key] = $this->sanitizePayload($value);
            }
        }
        
        return $payload;
    }

    /**
     * Extract error message from response
     */
    protected function extractErrorMessage(Response $response): ?string
    {
        try {
            $content = json_decode($response->getContent(), true);
            return $content['message'] ?? $content['error'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
