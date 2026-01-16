<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Hod;
use App\Models\AuditLog;
use App\Services\SessionMonitoringService;

class HodAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('hod.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $hod = Hod::with(['user', 'department'])
            ->where('staff_id', $request->staff_id)
            ->where('is_active', true)
            ->first();

        if (!$hod || !$hod->user) {
            return redirect()->back()->withErrors(['staff_id' => 'Invalid staff ID or HOD not found.']);
        }

        if (!Hash::check($request->password, $hod->user->password)) {
            return redirect()->back()->withErrors(['password' => 'Invalid password.']);
        }

        // Login the HOD
        Auth::guard('hod')->login($hod);
        
        // Update last login
        $hod->update(['last_login_at' => now()]);
        
        // Regenerate session for security
        $request->session()->regenerate();
        
        // Initialize session activity tracking
        $request->session()->put('hod_last_activity', now()->timestamp);
        $request->session()->put('hod_last_update', now()->timestamp);

        return redirect()->route('hod.dashboard');
    }

    public function logout(Request $request)
    {
        // Mark session as ended
        $sessionMonitoringService = new SessionMonitoringService();
        $sessionMonitoringService->endSession(session()->getId());
        
        Auth::guard('hod')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function dashboard()
    {
        $hod = Auth::guard('hod')->user();
        
        if (!$hod) {
            return redirect()->route('hod.login');
        }

        return view('hod.dashboard', compact('hod'));
    }
}