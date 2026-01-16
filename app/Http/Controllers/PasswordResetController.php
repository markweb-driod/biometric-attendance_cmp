<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PasswordResetService;
use App\Models\PasswordResetOtp;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP for password reset
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'otp_method' => 'required|in:email,sms',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->passwordResetService->sendOtp(
            $request->identifier,
            $request->otp_method,
            $request->ip(),
            $request->userAgent()
        );

        if ($result['success']) {
            return redirect()->route('password.verify-otp', ['otp_id' => $result['otp_id']])
                ->with('success', $result['message'])
                ->with('flash_message', $result['message'])
                ->with('flash_type', 'success');
        }

        return redirect()->back()
            ->withErrors(['identifier' => $result['message']])
            ->with('flash_message', $result['message'])
            ->with('flash_type', 'error')
            ->withInput();
    }

    /**
     * Show OTP verification form
     */
    public function verifyOtpForm(Request $request, $otp_id = null)
    {
        // If OTP ID is provided, validate it
        if ($otp_id) {
            $otpRecord = PasswordResetOtp::find($otp_id);
            if (!$otpRecord || !$otpRecord->isValid()) {
                return redirect()->route('password.forgot')
                    ->withErrors(['otp' => 'Invalid or expired OTP session.']);
            }

            session(['password_reset_otp_id' => $otp_id]);
            session(['password_reset_identifier' => $otpRecord->identifier]);
            session(['password_reset_user_type' => $otpRecord->user_type]);
        }

        // Check if session has required data
        if (!session('password_reset_identifier')) {
            return redirect()->route('password.forgot')
                ->withErrors(['otp' => 'Please request a password reset first.']);
        }

        return view('auth.verify-otp', [
            'identifier' => session('password_reset_identifier'),
            'user_type' => session('password_reset_user_type'),
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $identifier = session('password_reset_identifier');
        $userType = session('password_reset_user_type');

        if (!$identifier || !$userType) {
            return redirect()->route('password.forgot')
                ->withErrors(['otp' => 'Session expired. Please request a new OTP.']);
        }

        $otpRecord = $this->passwordResetService->verifyOtp($identifier, $userType, $request->otp);

        if (!$otpRecord) {
            return redirect()->back()
                ->withErrors(['otp' => 'Invalid or expired OTP.'])
                ->with('flash_message', 'Invalid or expired OTP.')
                ->with('flash_type', 'error');
        }

        // Store verified OTP ID in session
        session(['password_reset_verified_otp_id' => $otpRecord->id]);

        return redirect()->route('password.reset')
            ->with('success', 'OTP verified successfully. Please set your new password.')
            ->with('flash_message', 'OTP verified successfully. Please set your new password.')
            ->with('flash_type', 'success');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm()
    {
        if (!session('password_reset_verified_otp_id')) {
            return redirect()->route('password.forgot')
                ->withErrors(['password' => 'Please verify your OTP first.']);
        }

        return view('auth.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $identifier = session('password_reset_identifier');
        $userType = session('password_reset_user_type');
        $otpId = session('password_reset_verified_otp_id');

        if (!$identifier || !$userType || !$otpId) {
            return redirect()->route('password.forgot')
                ->withErrors(['password' => 'Session expired. Please start over.']);
        }

        $otpRecord = PasswordResetOtp::find($otpId);
        if (!$otpRecord || !$otpRecord->isValid()) {
            return redirect()->route('password.forgot')
                ->withErrors(['password' => 'OTP expired. Please request a new one.']);
        }

        $result = $this->passwordResetService->resetPassword(
            $identifier,
            $userType,
            $request->password,
            $otpRecord
        );

        if ($result['success']) {
            // Clear password reset session
            $request->session()->forget([
                'password_reset_otp_id',
                'password_reset_identifier',
                'password_reset_user_type',
                'password_reset_verified_otp_id',
            ]);

            return redirect()->route('login')
                ->with('success', 'Password reset successfully. Please login with your new password.')
                ->with('flash_message', 'Password reset successfully. Please login with your new password.')
                ->with('flash_type', 'success');
        }

        return redirect()->back()
            ->withErrors(['password' => $result['message']])
            ->with('flash_message', $result['message'])
            ->with('flash_type', 'error');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $identifier = session('password_reset_identifier');
        $userType = session('password_reset_user_type');
        $otpId = session('password_reset_otp_id');

        if (!$identifier || !$userType) {
            return redirect()->route('password.forgot')
                ->withErrors(['otp' => 'Session expired. Please request a new password reset.']);
        }

        // Get the OTP method from the last request
        $otpRecord = PasswordResetOtp::find($otpId);
        if (!$otpRecord) {
            return redirect()->route('password.forgot')
                ->withErrors(['otp' => 'Invalid session. Please request a new password reset.']);
        }

        $result = $this->passwordResetService->sendOtp(
            $identifier,
            $otpRecord->otp_method,
            $request->ip(),
            $request->userAgent()
        );

        if ($result['success']) {
            return redirect()->route('password.verify-otp', ['otp_id' => $result['otp_id']])
                ->with('success', 'OTP resent successfully.')
                ->with('flash_message', 'OTP resent successfully.')
                ->with('flash_type', 'success');
        }

        return redirect()->back()
            ->withErrors(['otp' => $result['message']])
            ->with('flash_message', $result['message'])
            ->with('flash_type', 'error');
    }
}
