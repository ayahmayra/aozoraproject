<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedRedirect
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
                return redirect()->secure(route('admin.dashboard'));
            }
            
            // Check if user is pending verification
            if ($user->isPending() || $user->isInactive()) {
                return redirect()->route('verification.pending');
            }
            
            // Redirect based on user role for verified users
            if ($user->hasRole('parent')) {
                return redirect()->secure(route('parent.dashboard'));
            } elseif ($user->hasRole('teacher')) {
                return redirect()->secure(route('teacher.dashboard'));
            } elseif ($user->hasRole('student')) {
                return redirect()->secure(route('student.dashboard'));
            }
        }

        return $next($request);
    }
}
