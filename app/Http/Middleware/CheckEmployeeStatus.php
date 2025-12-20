<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->employee) {
            if ($user->employee->employment_status !== 'active') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your account is not active. Please contact HR.');
            }
        }

        return $next($request);
    }
}
