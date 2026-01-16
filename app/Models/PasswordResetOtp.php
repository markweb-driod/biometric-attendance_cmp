<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'user_type',
        'otp_code',
        'otp_method',
        'expires_at',
        'verified_at',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Generate a 6-digit OTP
     */
    public static function generateOtp(): string
    {
        return str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP record (deprecated - use direct create with hashed OTP)
     * Kept for backward compatibility but not used in service
     */
    public static function createOtp(
        string $identifier,
        string $userType,
        string $otpMethod,
        string $ipAddress = null,
        string $userAgent = null
    ): self {
        $otp = self::generateOtp();
        $expiresAt = now()->addMinutes(15);

        return self::create([
            'identifier' => $identifier,
            'user_type' => $userType,
            'otp_code' => Hash::make($otp),
            'otp_method' => $otpMethod,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Verify the provided OTP
     */
    public function verifyOtp(string $otp): bool
    {
        // Check if already used
        if ($this->used_at !== null) {
            return false;
        }

        // Check if expired
        if ($this->expires_at->isPast()) {
            return false;
        }

        // Verify OTP
        if (Hash::check($otp, $this->otp_code)) {
            $this->update(['verified_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * Check if OTP is valid (not expired and not used)
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture() && $this->used_at === null;
    }

    /**
     * Get the plain OTP (only for newly created, not from database)
     * Note: This won't work for existing records since OTP is hashed
     */
    public function getPlainOtp(): ?string
    {
        // This method is only useful right after generation
        // Once saved to DB, OTP is hashed and cannot be retrieved
        return null;
    }

    /**
     * Invalidate all existing OTPs for an identifier
     */
    public static function invalidateExisting(string $identifier, string $userType): void
    {
        self::where('identifier', $identifier)
            ->where('user_type', $userType)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }

    /**
     * Check rate limit (max 3 OTP requests per hour per identifier)
     */
    public static function checkRateLimit(string $identifier, string $userType): bool
    {
        $count = self::where('identifier', $identifier)
            ->where('user_type', $userType)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        return $count < 3;
    }

    /**
     * Get active OTP for identifier
     */
    public static function getActiveOtp(string $identifier, string $userType): ?self
    {
        return self::where('identifier', $identifier)
            ->where('user_type', $userType)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}
