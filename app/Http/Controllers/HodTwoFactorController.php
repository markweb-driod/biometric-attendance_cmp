<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Hod;
use PragmaRX\Google2FA\Google2FA;

class HodTwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA verification form
     */
    public function show(Request $request)
    {
        $hod = Auth::guard('hod')->user();
        
        if (!$hod || !$hod->user) {
            return redirect('/login');
        }

        return view('hod.two-factor.verify', compact('hod'));
    }

    /**
     * Show 2FA setup page with QR code
     */
    public function setup()
    {
        $hod = Auth::guard('hod')->user();
        
        if (!$hod || !$hod->user) {
            return redirect('/login');
        }

        if ($hod->user->hasTwoFactorEnabled()) {
            return redirect()->route('hod.dashboard')->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();
        session(['hod_2fa_temp_secret' => $secret]);

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'Biometric Attendance'),
            $hod->user->email,
            $secret
        );

        return view('hod.two-factor.setup', compact('qrCodeUrl', 'secret'));
    }

    /**
     * Confirm and enable 2FA
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|digits:6',
        ]);

        $hod = Auth::guard('hod')->user();
        
        if (!$hod || !$hod->user) {
            return redirect('/login')->with('error', 'Session expired');
        }

        $tempSecret = session('hod_2fa_temp_secret');

        if (!$tempSecret) {
            return redirect()->route('hod.two-factor.setup')->withErrors(['two_factor_code' => 'Session expired. Please start over.']);
        }

        // Verify the code
        $valid = $this->google2fa->verifyKey($tempSecret, $request->two_factor_code, 2);

        if ($valid) {
            $hod->user->enableTwoFactor($tempSecret);
            session()->forget('hod_2fa_temp_secret');
            return redirect()->route('hod.dashboard')->with('success', 'Two-factor authentication has been enabled successfully.');
        }

        return back()->withErrors(['two_factor_code' => 'Invalid verification code. Please try again.']);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $hod = Auth::guard('hod')->user();
        
        if (!$hod || !$hod->user) {
            return redirect('/login')->with('error', 'Session expired');
        }

        if (!Hash::check($request->password, $hod->user->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        $hod->user->disableTwoFactor();
        session()->forget('hod_2fa_verified');
        session()->forget('hod_2fa_verified_at');

        return redirect()->route('hod.dashboard')->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Verify 2FA code during login/access
     */
    public function verify(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|string|size:6',
        ]);

        $hod = Auth::guard('hod')->user();
        
        if (!$hod || !$hod->user) {
            return redirect('/login')->with('error', 'Session expired');
        }

        if (!$hod->user->hasTwoFactorEnabled()) {
            return back()->withErrors(['two_factor_code' => 'Two-factor authentication is not enabled for this account.']);
        }

        $secret = $hod->user->getTwoFactorSecret();
        $valid = $this->google2fa->verifyKey($secret, $request->two_factor_code, 2);

        if ($valid) {
            // Mark as verified
            session([
                'hod_2fa_verified' => true,
                'hod_2fa_verified_at' => now(),
            ]);

            // Check if we need to execute a waiver after 2FA
            $waiverData = session('waiver_data');
            if ($waiverData && session('hod_2fa_intended_url') === route('hod.exam.api.eligibility.execute-waiver')) {
                session()->forget('hod_2fa_intended_url');
                return redirect()->route('hod.exam.eligibility')
                    ->with('success', 'Two-factor authentication verified. Proceeding with waiver...')
                    ->with('execute_waiver', true);
            }

            // Redirect to intended URL
            $intendedUrl = session('hod_2fa_intended_url', route('hod.dashboard'));
            session()->forget('hod_2fa_intended_url');

            return redirect($intendedUrl)->with('success', 'Two-factor authentication verified successfully');
        }

        return redirect()->back()->with('error', 'Invalid two-factor authentication code');
    }
}

