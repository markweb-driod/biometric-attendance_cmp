<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'resource_type',
        'resource_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id',
        'user_type',
        'user_id',
        'department_id',
        'severity',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Prevent updates and deletes for immutability
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->morphTo();
    }

    public function resource()
    {
        return $this->morphTo('resource', 'resource_type', 'resource_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByResource($query, $resourceType, $resourceId = null)
    {
        $query = $query->where('resource_type', $resourceType);
        
        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }
        
        return $query;
    }

    public function scopeByUser($query, $userType, $userId)
    {
        return $query->where('user_type', $userType)
                    ->where('user_id', $userId);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeHigh($query)
    {
        return $query->whereIn('severity', ['high', 'critical']);
    }

    // Static method to create audit log entries
    public static function log($action, $resourceType, $description, $user = null, $resourceId = null, $oldValues = null, $newValues = null, $severity = 'medium')
    {
        return self::create([
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'user_type' => $user ? get_class($user) : null,
            'user_id' => $user ? $user->id : null,
            'department_id' => $user && method_exists($user, 'department') ? $user->department_id : null,
            'severity' => $severity,
        ]);
    }

    // Override to prevent updates
    public function update(array $attributes = [], array $options = [])
    {
        throw new \Exception('Audit logs are immutable and cannot be updated.');
    }

    // Override to prevent deletes
    public function delete()
    {
        throw new \Exception('Audit logs are immutable and cannot be deleted.');
    }
}
