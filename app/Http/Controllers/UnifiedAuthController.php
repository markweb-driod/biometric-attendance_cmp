<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Superadmin;
use App\Models\Lecturer;
use App\Models\Hod;
use App\Services\SessionMonitoringService;
use App\Services\SessionNotificationService;

class UnifiedAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.unified-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->identifier;
        $password = $request->password;

        // Detect user type based on identifier format
        $userType = $this->detectUserType($identifier);

        if (!$userType) {
            return redirect()->back()->withErrors(['identifier' => 'Invalid format. Please use email for admin or staff ID for lecturers/HODs.']);
        }

        // Authenticate based on user type
        switch ($userType) {
            case 'superadmin':
                return $this->authenticateSuperadmin($identifier, $password);
            case 'lecturer':
                return $this->authenticateLecturer($identifier, $password);
            case 'hod':
                return $this->authenticateHod($identifier, $password);
            default:
                return redirect()->back()->withErrors(['identifier' => 'User type not recognized.']);
        }
    }

    private function detectUserType($identifier)
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'superadmin';
        }
        
        // Normalize identifier to uppercase for case-insensitive matching
        $normalized = strtoupper(trim($identifier));
        
        if (strpos($normalized, 'LEC') === 0) {
            return 'lecturer';
        } elseif (strpos($normalized, 'HOD') === 0) {
            return 'hod';
        }
        return null;
    }

    private function authenticateSuperadmin($email, $password)
    {
        // Normalize email to lowercase
        $normalizedEmail = strtolower(trim($email));
        
        // Cache key for superadmin lookup
        $cacheKey = "superadmin_{$normalizedEmail}";
        
        $superadmin = Cache::remember($cacheKey, 300, function () use ($normalizedEmail) {
            return Superadmin::where('email', $normalizedEmail)->first();
        });
        
        if (!$superadmin || !Hash::check($password, $superadmin->password)) {
            Log::warning('Superadmin Authentication Failed', [
                'email' => $normalizedEmail,
                'found' => $superadmin ? true : false
            ]);
            return redirect()->back()->withErrors(['password' => 'Invalid email or password.']);
        }

        Auth::guard('superadmin')->login($superadmin);
        $request = request();
        $request->session()->regenerate();

        // Track session and send notification
        // $sessionMonitoringService = new SessionMonitoringService();
        // $session = $sessionMonitoringService->createSession($superadmin, 'Superadmin');
        // if ($session) {
        //     $notificationService = new SessionNotificationService();
        //     $notificationService->notifySuperadmins($session);
        // }

        // Check if 2FA is enabled
        if ($superadmin->hasTwoFactorEnabled()) {
            session(['superadmin_2fa_intended_url' => '/superadmin/dashboard']);
            return redirect()->route('superadmin.2fa.show');
        }

        return redirect()->intended('/superadmin/dashboard');
    }

    private function authenticateLecturer($staffId, $password)
    {
        // Normalize staff_id to uppercase for case-insensitive lookup
        $normalizedStaffId = strtoupper(trim($staffId));
        
        // Cache key for lecturer lookup
        $cacheKey = "lecturer_{$normalizedStaffId}";
        
        $lecturer = Cache::remember($cacheKey, 300, function () use ($normalizedStaffId) {
            return Lecturer::with('user')
                ->where('staff_id', $normalizedStaffId)
                ->where('is_active', true)
                ->first();
        });

        if (!$lecturer || !$lecturer->user) {
            Log::warning('Lecturer Authentication Failed: Lecturer not found', [
                'staff_id' => $normalizedStaffId,
                'input' => $staffId
            ]);
            return redirect()->back()->withErrors(['identifier' => 'Invalid staff ID or lecturer not found.']);
        }

        if (!Hash::check($password, $lecturer->user->password)) {
            Log::warning('Lecturer Authentication Failed: Invalid password', [
                'staff_id' => $normalizedStaffId,
                'lecturer_id' => $lecturer->id
            ]);
            return redirect()->back()->withErrors(['password' => 'Invalid password.']);
        }

        Auth::guard('lecturer')->login($lecturer);
        $request = request();
        $request->session()->regenerate();

        // Track session and send notification
        $sessionMonitoringService = new SessionMonitoringService();
        $session = $sessionMonitoringService->createSession($lecturer, 'Lecturer');
        if ($session) {
            $notificationService = new SessionNotificationService();
            $notificationService->notifySuperadmins($session);
        }

        // Check if 2FA is enabled
        if ($lecturer->user && $lecturer->user->hasTwoFactorEnabled()) {
            session(['lecturer_2fa_intended_url' => '/lecturer/dashboard']);
            return redirect()->route('lecturer.2fa.show');
        }

        return redirect()->intended('/lecturer/dashboard');
    }

    private function authenticateHod($staffId, $password)
    {
        // Normalize staff_id to uppercase for case-insensitive lookup
        $normalizedStaffId = strtoupper(trim($staffId));
        
        // Don't use cache for login to avoid stale data
        $hod = Hod::with(['user', 'department'])
            ->where('staff_id', $normalizedStaffId)
            ->where('is_active', true)
            ->first();

        if (!$hod || !$hod->user) {
            Log::warning('HOD Authentication Failed: HOD not found', [
                'staff_id' => $normalizedStaffId,
                'input' => $staffId
            ]);
            return redirect()->back()->withErrors(['identifier' => 'Invalid staff ID or HOD not found.']);
        }

        if (!Hash::check($password, $hod->user->password)) {
            Log::warning('HOD Authentication Failed: Invalid password', [
                'staff_id' => $normalizedStaffId,
                'hod_id' => $hod->id
            ]);
            return redirect()->back()->withErrors(['password' => 'Invalid password.']);
        }

        Log::info('HOD Authentication Successful', [
            'staff_id' => $normalizedStaffId,
            'hod_id' => $hod->id
        ]);
        
        Auth::guard('hod')->login($hod);

        // DEBUG: Verify user is logged in
        if (Auth::guard('hod')->check()) {
             Log::info('HOD Guard Check Passed immediately after login');
        } else {
             Log::error('HOD Guard Check FAILED immediately after login');
             return redirect()->back()->withErrors(['identifier' => 'Login system error: Session not persisting.']);
        }
        
        // Update last login
        $hod->update(['last_login_at' => now()]);
        
        $request = request();
        $request->session()->regenerate();
        
        // Initialize session activity tracking
        $request->session()->put('hod_last_activity', now()->timestamp);
        $request->session()->put('hod_last_update', now()->timestamp);

        Log::info('HOD Session initialized, redirecting to dashboard');
        
        // Track session and send notification
        // $sessionMonitoringService = new SessionMonitoringService();
        // $session = $sessionMonitoringService->createSession($hod, 'Hod');
        // if ($session) {
        //     $notificationService = new SessionNotificationService();
        //     $notificationService->notifySuperadmins($session);
        // }
        
        // Check if 2FA is enabled
        if ($hod->user && $hod->user->hasTwoFactorEnabled()) {
            session(['hod_2fa_intended_url' => '/hod/dashboard']);
            return redirect()->route('hod.two-factor.show');
        }
        
        return redirect()->intended('/hod/dashboard');
    }
}
