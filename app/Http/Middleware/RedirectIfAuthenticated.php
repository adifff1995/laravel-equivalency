<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect authenticated users to their role-appropriate dashboard.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                $redirectTo = $user->isAdmin()
                    ? route('admin.requests.index')
                    : route('academic.requests.index');

                return redirect($redirectTo);
            }
        }

        return $next($request);
    }
}
