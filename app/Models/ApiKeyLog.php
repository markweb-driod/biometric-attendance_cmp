<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKeyLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'api_key_id',
        'endpoint',
        'method',
        'ip_address',
        'user_agent',
        'response_status',
        'response_time_ms',
        'request_payload',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship to API key
     */
    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * Scope for successful requests
     */
    public function scopeSuccessful($query)
    {
        return $query->whereNotNull('response_status')
                     ->where('response_status', '>=', 200)
                     ->where('response_status', '<', 300);
    }

    /**
     * Scope for failed requests
     */
    public function scopeFailed($query)
    {
        return $query->where(function($q) {
            $q->whereNull('response_status')
              ->orWhere('response_status', '>=', 400);
        });
    }

    /**
     * Scope for errors
     */
    public function scopeErrors($query)
    {
        return $query->whereNotNull('error_message');
    }
}
