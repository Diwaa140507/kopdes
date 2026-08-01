<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekWajibGantiPassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('web')->check()) {
            $anggota = Auth::guard('web')->user();

            if ($anggota->wajib_ganti_password && ! $request->routeIs('ganti-password', 'ganti-password.store', 'logout')) {
                return redirect()->route('ganti-password');
            }
        }

        return $next($request);
    }
}