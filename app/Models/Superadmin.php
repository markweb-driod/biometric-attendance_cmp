<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Superadmin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'is_active',
        'two_factor_secret',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

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