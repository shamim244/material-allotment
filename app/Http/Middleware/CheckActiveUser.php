<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Allow if admin is impersonating this user
            if (session()->has('impersonator_id')) {
                return $next($request);
            }

            // Check if individual user is deactivated
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Your account has been deactivated. Please contact the administrator.');
            }

            // Check if user's company is suspended
            if ($user->company && $user->company->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Your contractor agency has been suspended. Please contact platform support.');
            }
        }

        return $next($request);
    }
}
