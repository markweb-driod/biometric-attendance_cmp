<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Superadmin;
use Illuminate\Support\Facades\Hash;

class SuperadminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('superadmin.login');
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
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/superadmin/login');
    }
} 