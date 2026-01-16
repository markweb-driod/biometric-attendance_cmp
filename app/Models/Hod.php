<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Hod extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'department_id',
        'staff_id',
        'title',
        'phone',
        'office_location',
        'is_active',
        'appointed_at',
        'last_login_at',
        'permissions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'appointed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'permissions' => 'array',
    ];

    protected $hidden = [
        'permissions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function lecturers()
    {
        return $this->hasManyThrough(Lecturer::class, Department::class, 'id', 'department_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Department::class, 'id', 'department_id');
    }

    public function examEligibilities()
    {
        return $this->hasMany(ExamEligibility::class, 'overridden_by');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'user');
    }

    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->full_name : 'Unknown';
    }

    public function getDisplayNameAttribute()
    {
        $title = $this->title ? $this->title . ' ' : '';
        return $title . $this->full_name;
    }

    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return false;
        }
        
        return in_array($permission, $this->permissions);
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->user ? $this->user->password : null;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     */
    public function getPasswordAttribute()
    {
        return $this->user ? $this->user->password : null;
    }

    /**
     * Get the email address for the user.
     */
    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : null;
    }

    /**
     * Get the name for the user.
     */
    public function getNameAttribute()
    {
        return $this->user ? $this->user->full_name : null;
    }
}
