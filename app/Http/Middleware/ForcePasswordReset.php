<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordReset
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user has the force_password_reset attribute
            if ($user && isset($user->force_password_reset) && $user->force_password_reset) {
                // Allow access to password reset routes
                if (!$request->routeIs('password.*') && !$request->routeIs('logout')) {
                    return redirect()->route('password.reset.forced')
                        ->with('warning', 'You must change your password before continuing.');
                }
            }
        }

        return $next($request);
    }
}