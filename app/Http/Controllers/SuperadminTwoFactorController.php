<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use App\Models\Superadmin;

class SuperadminTwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA verification form
     */
    public function show()
    {
        return view('superadmin.2fa');
    }

    /**
     * Show 2FA setup page with QR code
     */
    public function setup()
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        if ($superadmin->hasTwoFactorEnabled()) {
            return redirect()->route('superadmin.settings')->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();
        session(['superadmin_2fa_temp_secret' => $secret]);

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'Biometric Attendance'),
            $superadmin->email,
            $secret
        );

        return view('superadmin.two-factor.setup', compact('qrCodeUrl', 'secret'));
    }

    /**
     * Confirm and enable 2FA
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $superadmin = Auth::guard('superadmin')->user();
        $tempSecret = session('superadmin_2fa_temp_secret');

        if (!$tempSecret) {
            return redirect()->route('superadmin.2fa.setup')->withErrors(['code' => 'Session expired. Please start over.']);
        }

        // Verify the code
        $valid = $this->google2fa->verifyKey($tempSecret, $request->code, 2); // 2 = tolerance window

        if ($valid) {
            $superadmin->enableTwoFactor($tempSecret);
            session()->forget('superadmin_2fa_temp_secret');
            return redirect()->route('superadmin.settings')->with('success', 'Two-factor authentication has been enabled successfully.');
        }

        return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $superadmin = Auth::guard('superadmin')->user();

        if (!\Hash::check($request->password, $superadmin->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        $superadmin->disableTwoFactor();
        session()->forget('superadmin_2fa_verified');

        return redirect()->route('superadmin.settings')->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Verify 2FA code during login/access
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $superadmin = Auth::guard('superadmin')->user();
        
        if (!$superadmin || !$superadmin->hasTwoFactorEnabled()) {
            return back()->withErrors(['code' => 'Two-factor authentication is not enabled for this account.']);
        }

        $secret = $superadmin->getTwoFactorSecret();
        $valid = $this->google2fa->verifyKey($secret, $request->code, 2);

        if ($valid) {
            session(['superadmin_2fa_verified' => true]);
            $redirect = session('superadmin_2fa_intended_url', route('superadmin.dashboard'));
            session()->forget('superadmin_2fa_intended_url');
            return redirect($redirect)->with('success', 'Two-factor authentication successful.');
        }

        return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
    }
}
