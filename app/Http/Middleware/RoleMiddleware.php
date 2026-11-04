<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->status !== 'active') {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is suspended.',
            ]);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
