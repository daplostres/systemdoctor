<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isDoctor()) {
            abort(403, 'Halaman ini khusus untuk Dokter.');
        }

        return $next($request);
    }
}

