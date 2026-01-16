<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Superadmin;
use Illuminate\Support\Facades\Hash;
use App\Services\SessionMonitoringService;

class SuperadminAuthController extends Controller
{
    public function showLoginForm()
    {
        return redirect('/login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $superadmin = Superadmin::where('email', $request->email)->first();
        if ($superadmin && Hash::check($request->password, $superadmin->password)) {
            Auth::guard('superadmin')->login($superadmin);
            return redirect()->intended('/superadmin/dashboard');
        }
        return back()->with('error', 'Invalid email or password');
    }

    public function logout(Request $request)
    {
        // Mark session as ended
        $sessionMonitoringService = new SessionMonitoringService();
        $sessionMonitoringService->endSession(session()->getId());
        
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
} 
