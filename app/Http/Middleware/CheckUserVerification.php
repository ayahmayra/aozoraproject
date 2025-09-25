<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Skip verification check for admin users
            if ($user->hasRole('admin')) {
                return $next($request);
            }
            
            // Check if user is pending verification
            if ($user->isPending() || $user->isInactive()) {
                // Allow access to verification pending page and logout
                if ($request->routeIs('verification.pending') || $request->routeIs('logout')) {
                    return $next($request);
                }
                
                // Redirect to verification pending page
                return redirect()->route('verification.pending');
            }
        }
        
        return $next($request);
    }
}
