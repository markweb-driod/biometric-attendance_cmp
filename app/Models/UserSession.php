<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_type',
        'user_id',
        'identifier',
        'full_name',
        'login_at',
        'logout_at',
        'last_activity_at',
        'status',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
        'timezone',
        'activity_trail',
        'department_id',
        'department_name',
        'terminated_by',
        'terminated_at',
        'termination_reason',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'terminated_at' => 'datetime',
        'activity_trail' => 'array',
    ];

    /**
     * Get the user relationship based on user_type
     */
    public function user()
    {
        return $this->morphTo('user', 'user_type', 'user_id');
    }

    /**
     * Get the superadmin user
     */
    public function superadmin()
    {
        return $this->belongsTo(Superadmin::class, 'user_id')
            ->where('user_type', 'Superadmin');
    }

    /**
     * Get the lecturer user
     */
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'user_id')
            ->where('user_type', 'Lecturer');
    }

    /**
     * Get the HOD user
     */
    public function hod()
    {
        return $this->belongsTo(Hod::class, 'user_id')
            ->where('user_type', 'Hod');
    }

    /**
     * Get the department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the superadmin who terminated the session
     */
    public function terminator()
    {
        return $this->belongsTo(Superadmin::class, 'terminated_by');
    }

    /**
     * Scope to get active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get ended sessions
     */
    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    /**
     * Scope to get expired sessions
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope to get terminated sessions
     */
    public function scopeTerminated($query)
    {
        return $query->where('status', 'terminated');
    }

    /**
     * Scope to get sessions by user type
     */
    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope to get recent sessions
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('login_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get sessions by user
     */
    public function scopeByUser($query, $userType, $userId)
    {
        return $query->where('user_type', $userType)
                    ->where('user_id', $userId);
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get session duration in human readable format
     */
    public function getDurationAttribute(): string
    {
        $endTime = $this->logout_at ?? $this->terminated_at ?? now();
        return $this->login_at->diffForHumans($endTime, true);
    }

    /**
     * Get session duration in seconds
     */
    public function getDurationInSecondsAttribute(): int
    {
        $endTime = $this->logout_at ?? $this->terminated_at ?? now();
        return $this->login_at->diffInSeconds($endTime);
    }

    /**
     * Check if session is idle (no activity in last 30 minutes)
     */
    public function isIdle(): bool
    {
        if (!$this->last_activity_at) {
            return false;
        }
        return $this->last_activity_at->addMinutes(30)->isPast();
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Add activity to trail
     */
    public function addActivity(string $action, array $metadata = []): void
    {
        $trail = $this->activity_trail ?? [];
        $trail[] = [
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'metadata' => $metadata,
        ];
        $this->update([
            'activity_trail' => $trail,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Mark session as ended
     */
    public function markAsEnded(): void
    {
        $this->update([
            'status' => 'ended',
            'logout_at' => now(),
        ]);
    }

    /**
     * Mark session as terminated
     */
    public function terminate(int $terminatedBy, string $reason = null): void
    {
        $this->update([
            'status' => 'terminated',
            'terminated_by' => $terminatedBy,
            'terminated_at' => now(),
            'termination_reason' => $reason,
        ]);
    }

    /**
     * Mark session as expired
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
