<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Clidual\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hod = auth()->guard('hod')->user();
        $superadmin = auth()->guard('superadmin')->user();
        
        if (!$hod && !$superadmin) {
            // Check if this is an AJAX request
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login first',
                    'requires_2fa' => true
                ], 401);
            }
            return redirect('/login')->with('error', 'Please login first');
        }

        // Check if 2FA is required for this action
        if ($hod) {
            // Check if HOD has 2FA enabled and if it's verified
            $has2FAEnabled = $hod->user && $hod->user->hasTwoFactorEnabled();
            $twoFactorVerified = session('hod_2fa_verified');
            
            if ($has2FAEnabled && !$twoFactorVerified) {
                // Store intended URL
                session(['hod_2fa_intended_url' => $request->fullUrl()]);
                
                // Check if this is an AJAX request
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Two-factor authentication required',
                        'requires_2fa' => true,
                        'redirect' => route('hod.two-factor.show')
                    ], 302);
                }
                
                // Redirect to 2FA verification page
                return redirect()->route('hod.two-factor.show')
                    ->with('warning', 'Two-factor authentication required for this action.');
            }
        } elseif ($superadmin) {
            // Check if superadmin has 2FA enabled and if it's verified
            $has2FAEnabled = $superadmin->hasTwoFactorEnabled();
            $twoFactorVerified = session('superadmin_2fa_verified');
            
            if ($has2FAEnabled && !$twoFactorVerified) {
                // Store intended URL
                session(['superadmin_2fa_intended_url' => $request->fullUrl()]);
                
                // Check if this is an AJAX request
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Two-factor authentication required',
                        'requires_2fa' => true,
                        'redirect' => route('superadmin.2fa.show')
                    ], 302);
                }
                
                // Redirect to 2FA verification page
                return redirect()->route('superadmin.2fa.show')
                    ->with('warning', 'Two-factor authentication required for this action.');
            }
        }
        
        // Check lecturer guard
        $lecturer = auth()->guard('lecturer')->user();
        if ($lecturer) {
            $has2FAEnabled = $lecturer->user && $lecturer->user->hasTwoFactorEnabled();
            $twoFactorVerified = session('lecturer_2fa_verified');
            
            if ($has2FAEnabled && !$twoFactorVerified) {
                session(['lecturer_2fa_intended_url' => $request->fullUrl()]);
                
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Two-factor authentication required',
                        'requires_2fa' => true,
                        'redirect' => route('lecturer.2fa.show')
                    ], 302);
                }
                
                return redirect()->route('lecturer.2fa.show')
                    ->with('warning', 'Two-factor authentication required for this action.');
            }
        }

        return $next($request);
    }
}

