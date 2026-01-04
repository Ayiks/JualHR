<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordReset
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->force_password_reset) {
            // Allow these routes without redirect
            $allowedRoutes = [
                'password.*',
                'logout',
                'profile.edit',
                'profile.update',
            ];

            foreach ($allowedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    return $next($request);
                }
            }

            // Redirect to password change
            return redirect()->route('profile.edit')
                ->with('warning', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}