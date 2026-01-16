<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'key',
        'secret_hash',
        'client_name',
        'client_contact',
        'is_active',
        'rate_limit_per_minute',
        'rate_limit_per_hour',
        'expires_at',
        'last_used_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'key',
        'secret_hash',
    ];

    /**
     * Generate a new API key and secret
     */
    public static function generateKeyPair(): array
    {
        $key = config('api.key_prefix', 'sk_') . Str::random(64);
        $secret = Str::random(32);
        
        return [
            'key' => $key,
            'key_hash' => Hash::make($key),
            'secret' => $secret,
            'secret_hash' => Hash::make($secret),
        ];
    }

    /**
     * Find API key by plain text key
     */
    public static function findByKey(string $key): ?self
    {
        $keys = static::where('is_active', true)->get();
        
        foreach ($keys as $apiKey) {
            if (Hash::check($key, $apiKey->key)) {
                return $apiKey;
            }
        }
        
        return null;
    }

    /**
     * Relationship to creator (Superadmin)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Superadmin::class, 'created_by');
    }

    /**
     * Relationship to logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ApiKeyLog::class);
    }

    /**
     * Check if API key is active
     */
    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Check if API key is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }

    /**
     * Check if API key can make a request (rate limit check is handled separately)
     */
    public function canMakeRequest(): bool
    {
        return $this->isActive();
    }

    /**
     * Log an API request
     */
    public function logRequest(array $data): ApiKeyLog
    {
        $this->update(['last_used_at' => now()]);
        
        return $this->logs()->create([
            'endpoint' => $data['endpoint'] ?? 'unknown',
            'method' => $data['method'] ?? 'GET',
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'response_status' => $data['response_status'] ?? null,
            'response_time_ms' => $data['response_time_ms'] ?? null,
            'request_payload' => $data['request_payload'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'created_at' => now(),
        ]);
    }

    /**
     * Increment usage counter
     */
    public function incrementUsage(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get total usage count
     */
    public function getTotalUsageCount(): int
    {
        return $this->logs()->count();
    }

    /**
     * Get usage count in time range
     */
    public function getUsageCount(?Carbon $start = null, ?Carbon $end = null): int
    {
        $query = $this->logs();
        
        if ($start) {
            $query->where('created_at', '>=', $start);
        }
        
        if ($end) {
            $query->where('created_at', '<=', $end);
        }
        
        return $query->count();
    }
}
