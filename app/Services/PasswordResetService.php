<?php

namespace App\Services;

use App\Models\PasswordResetOtp;
use App\Models\Superadmin;
use App\Models\Lecturer;
use App\Models\Hod;
use App\Models\User;
use App\Mail\PasswordResetAttemptMail;
use App\Mail\PasswordResetSuccessMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PasswordResetService
{
    /**
     * Detect user type from identifier
     */
    public function detectUserType(string $identifier): ?string
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'superadmin';
        } elseif (strpos($identifier, 'LEC') === 0) {
            return 'lecturer';
        } elseif (strpos($identifier, 'HOD') === 0) {
            return 'hod';
        }
        return null;
    }

    /**
     * Find user by identifier and type
     */
    public function findUser(string $identifier, string $userType): ?array
    {
        switch ($userType) {
            case 'superadmin':
                $user = Superadmin::where('email', $identifier)->first();
                if ($user) {
                    return [
                        'model' => $user,
                        'email' => $user->email,
                        'name' => $user->full_name,
                        'phone' => null,
                    ];
                }
                break;

            case 'lecturer':
                $lecturer = Lecturer::with('user')
                    ->where('staff_id', $identifier)
                    ->where('is_active', true)
                    ->first();
                if ($lecturer && $lecturer->user) {
                    return [
                        'model' => $lecturer->user,
                        'email' => $lecturer->user->email,
                        'name' => $lecturer->user->full_name,
                        'phone' => $lecturer->phone,
                    ];
                }
                break;

            case 'hod':
                $hod = Hod::with('user')
                    ->where('staff_id', $identifier)
                    ->where('is_active', true)
                    ->first();
                if ($hod && $hod->user) {
                    return [
                        'model' => $hod->user,
                        'email' => $hod->user->email,
                        'name' => $hod->user->full_name,
                        'phone' => $hod->phone,
                    ];
                }
                break;
        }

        return null;
    }

    /**
     * Send OTP for password reset
     */
    public function sendOtp(string $identifier, string $otpMethod, string $ipAddress = null, string $userAgent = null): array
    {
        // Detect user type
        $userType = $this->detectUserType($identifier);
        if (!$userType) {
            return ['success' => false, 'message' => 'Invalid identifier format.'];
        }

        // Find user
        $userData = $this->findUser($identifier, $userType);
        if (!$userData) {
            return ['success' => false, 'message' => 'User not found or inactive.'];
        }

        // Check rate limit
        if (!PasswordResetOtp::checkRateLimit($identifier, $userType)) {
            return ['success' => false, 'message' => 'Too many requests. Please try again later.'];
        }

        // Validate OTP method
        if ($otpMethod === 'sms' && empty($userData['phone'])) {
            return ['success' => false, 'message' => 'Phone number not available. Please use email instead.'];
        }

        // Invalidate existing OTPs
        PasswordResetOtp::invalidateExisting($identifier, $userType);

        // Generate OTP
        $otp = PasswordResetOtp::generateOtp();
        
        // Create OTP record with hashed OTP
        $expiresAt = now()->addMinutes(15);
        $otpRecord = PasswordResetOtp::create([
            'identifier' => $identifier,
            'user_type' => $userType,
            'otp_code' => \Hash::make($otp),
            'otp_method' => $otpMethod,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);

        // Send OTP via selected method
        try {
            if ($otpMethod === 'email') {
                $this->sendEmailOtp($userData['email'], $userData['name'], $otp);
            } elseif ($otpMethod === 'sms' && !empty($userData['phone'])) {
                $this->sendSmsOtp($userData['phone'], $otp);
            }

            // Send notification emails to user and all superadmins
            $this->notifyPasswordResetAttempt($userData, $userType, $identifier, $ipAddress);

            return [
                'success' => true,
                'message' => 'OTP sent successfully.',
                'otp_id' => $otpRecord->id,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'identifier' => $identifier,
                'user_type' => $userType,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'Failed to send OTP. Please try again.'];
        }
    }

    /**
     * Send OTP via email
     */
    private function sendEmailOtp(string $email, string $name, string $otp): void
    {
        Mail::raw(
            "Hello {$name},\n\nYour password reset OTP is: {$otp}\n\nThis OTP will expire in 15 minutes.\n\nIf you didn't request this, please ignore this email.",
            function ($message) use ($email) {
                $message->to($email)
                    ->subject('Password Reset OTP - NSUK Biometric Attendance System');
            }
        );
    }

    /**
     * Send OTP via SMS
     */
    private function sendSmsOtp(string $phone, string $otp): void
    {
        $smsService = new SmsService();
        $message = "Your password reset OTP is: {$otp}. This OTP will expire in 15 minutes. - NSUK Biometric Attendance System";
        $smsService->sendSms($phone, $message);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $identifier, string $userType, string $otp): ?PasswordResetOtp
    {
        $otpRecord = PasswordResetOtp::getActiveOtp($identifier, $userType);

        if (!$otpRecord) {
            return null;
        }

        if ($otpRecord->verifyOtp($otp)) {
            return $otpRecord;
        }

        return null;
    }

    /**
     * Reset password
     */
    public function resetPassword(string $identifier, string $userType, string $newPassword, PasswordResetOtp $otpRecord): array
    {
        // Find user
        $userData = $this->findUser($identifier, $userType);
        if (!$userData) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        // Update password
        $userModel = $userData['model'];
        $userModel->password = \Hash::make($newPassword);
        $userModel->save();

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Send success notifications
        $this->notifyPasswordResetSuccess($userData, $userType, $identifier);

        // Log password change
        Log::info('Password reset successful', [
            'identifier' => $identifier,
            'user_type' => $userType,
            'ip_address' => $otpRecord->ip_address,
        ]);

        return ['success' => true, 'message' => 'Password reset successfully.'];
    }

    /**
     * Notify user and superadmins about password reset attempt
     */
    private function notifyPasswordResetAttempt(array $userData, string $userType, string $identifier, ?string $ipAddress): void
    {
        try {
            // Notify user
            Mail::to($userData['email'])->send(
                new PasswordResetAttemptMail($userData, $userType, $identifier, $ipAddress)
            );

            // Notify all active superadmins
            $superadmins = Superadmin::where('is_active', true)->get();
            foreach ($superadmins as $superadmin) {
                Mail::to($superadmin->email)->send(
                    new PasswordResetAttemptMail($userData, $userType, $identifier, $ipAddress, $superadmin)
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send password reset attempt notifications', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify user and superadmins about successful password reset
     */
    private function notifyPasswordResetSuccess(array $userData, string $userType, string $identifier): void
    {
        try {
            // Notify user
            Mail::to($userData['email'])->send(
                new PasswordResetSuccessMail($userData, $userType, $identifier)
            );

            // Notify all active superadmins
            $superadmins = Superadmin::where('is_active', true)->get();
            foreach ($superadmins as $superadmin) {
                Mail::to($superadmin->email)->send(
                    new PasswordResetSuccessMail($userData, $userType, $identifier, $superadmin)
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send password reset success notifications', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
