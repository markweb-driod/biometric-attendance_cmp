<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'full_name',
        'password',
        'role',
        'is_active',
        'two_factor_secret',
        'two_factor_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Get the student record associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the lecturer record associated with the user.
     */
    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    /**
     * Get the superadmin record associated with the user.
     */
    public function superadmin()
    {
        return $this->hasOne(Superadmin::class, 'email', 'email');
    }

    /**
     * Check if 2FA is enabled for this user
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && !empty($this->two_factor_secret);
    }

    /**
     * Enable 2FA for this user
     */
    public function enableTwoFactor(string $secret): void
    {
        $this->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true,
        ]);
    }

    /**
     * Disable 2FA for this user
     */
    public function disableTwoFactor(): void
    {
        $this->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);
    }

    /**
     * Get the decrypted 2FA secret
     */
    public function getTwoFactorSecret(): ?string
    {
        if (empty($this->two_factor_secret)) {
            return null;
        }
        return decrypt($this->two_factor_secret);
    }
}
