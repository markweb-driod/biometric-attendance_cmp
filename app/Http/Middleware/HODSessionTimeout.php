<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class HODSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('hod')->check()) {
            return $next($request);
        }

        $hod = Auth::guard('hod')->user();
        Log::info('HOD Session Check', [
            'id' => $hod->id, 
            'last_activity' => $request->session()->get('hod_last_activity'), 
            'time' => now()->timestamp
        ]);
        $sessionTimeout = Config::get('auth.hod_session_timeout', 60); // Default 60 minutes
        
        // Check if this is an AJAX request
        $isAjax = $request->ajax() || $request->wantsJson();
        
        // Get last activity time from session
        $lastActivity = $request->session()->get('hod_last_activity');
        $currentTime = now()->timestamp;
        
        if ($lastActivity && ($currentTime - $lastActivity) > ($sessionTimeout * 60)) {
            // Session has expired
            Auth::guard('hod')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            if ($isAjax) {
                return response()->json([
                    'error' => 'Session expired',
                    'message' => 'Your session has expired due to inactivity. Please log in again.',
                    'redirect' => '/login'
                ], 401);
            }
            
            return redirect('/login')
                ->with('error', 'Your session has expired due to inactivity. Please log in again.');
        }
        
        // Update last activity time
        $request->session()->put('hod_last_activity', $currentTime);
        
        // Update HOD's last login time periodically (every 5 minutes)
        $lastUpdate = $request->session()->get('hod_last_update', 0);
        if (($currentTime - $lastUpdate) > 300) { // 5 minutes
            $hod->updateLastLogin();
            $request->session()->put('hod_last_update', $currentTime);
        }

        return $next($request);
    }
}