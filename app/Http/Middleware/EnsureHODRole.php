<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHODRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('hod')->check()) {
            return redirect('/login')->with('error', 'Please log in as HOD to access this page.');
        }

        $hod = auth('hod')->user();
        
        if (!$hod->is_active) {
            auth('hod')->logout();
            return redirect('/login')->with('error', 'Your HOD account has been deactivated.');
        }

        return $next($request);
    }
}
