<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasRole(...$roles)) {
            abort(403, 'Akun Anda ('.$user?->role_label.') tidak memiliki akses ke menu ini.');
        }

        return $next($request);
    }
}
