<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Lecturer;
use PragmaRX\Google2FA\Google2FA;

class LecturerTwoFactorController extends Controller
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
        $lecturer = Auth::guard('lecturer')->user();
        
        if (!$lecturer || !$lecturer->user) {
            return redirect('/login');
        }

        return view('lecturer.two-factor.verify', compact('lecturer'));
    }

    /**
     * Show 2FA setup page with QR code
     */
    public function setup()
    {
        $lecturer = Auth::guard('lecturer')->user();
        
        if (!$lecturer || !$lecturer->user) {
            return redirect('/login');
        }

        if ($lecturer->user->hasTwoFactorEnabled()) {
            return redirect()->route('lecturer.dashboard')->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();
        session(['lecturer_2fa_temp_secret' => $secret]);

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'Biometric Attendance'),
            $lecturer->user->email,
            $secret
        );

        return view('lecturer.two-factor.setup', compact('qrCodeUrl', 'secret'));
    }

    /**
     * Confirm and enable 2FA
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $lecturer = Auth::guard('lecturer')->user();
        
        if (!$lecturer || !$lecturer->user) {
            return redirect('/login')->with('error', 'Session expired');
        }

        $tempSecret = session('lecturer_2fa_temp_secret');

        if (!$tempSecret) {
            return redirect()->route('lecturer.2fa.setup')->withErrors(['code' => 'Session expired. Please start over.']);
        }

        // Verify the code
        $valid = $this->google2fa->verifyKey($tempSecret, $request->code, 2);

        if ($valid) {
            $lecturer->user->enableTwoFactor($tempSecret);
            session()->forget('lecturer_2fa_temp_secret');
            return redirect()->route('lecturer.dashboard')->with('success', 'Two-factor authentication has been enabled successfully.');
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

        $lecturer = Auth::guard('lecturer')->user();
        
        if (!$lecturer || !$lecturer->user) {
            return redirect('/login')->with('error', 'Session expired');
        }

        if (!Hash::check($request->password, $lecturer->user->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        $lecturer->user->disableTwoFactor();
        session()->forget('lecturer_2fa_verified');

        return redirect()->route('lecturer.dashboard')->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Verify 2FA code during login/access
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $lecturer = Auth::guard('lecturer')->user();
        
        if (!$lecturer || !$lecturer->user) {
            return redirect('/login')->with('error', 'Session expired');
        }

        if (!$lecturer->user->hasTwoFactorEnabled()) {
            return back()->withErrors(['code' => 'Two-factor authentication is not enabled for this account.']);
        }

        $secret = $lecturer->user->getTwoFactorSecret();
        $valid = $this->google2fa->verifyKey($secret, $request->code, 2);

        if ($valid) {
            session(['lecturer_2fa_verified' => true]);
            $redirect = session('lecturer_2fa_intended_url', route('lecturer.dashboard'));
            session()->forget('lecturer_2fa_intended_url');
            return redirect($redirect)->with('success', 'Two-factor authentication successful.');
        }

        return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
    }
}

