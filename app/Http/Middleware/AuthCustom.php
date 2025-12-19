<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthCustom
{
    public function handle($request, Closure $next)
    {
        // Terima kalau user sudah login via Laravel Auth OR ada flag session lama
        if (! Auth::check() && ! session('is_logged_in')) {
            return redirect()->route('login');
        }

        // Jika user login tapi akunnya dibekukan, blokir akses ke area yang memakai middleware ini
        if (Auth::check() && method_exists(Auth::user(), 'isFrozen') && Auth::user()->isFrozen()) {
            abort(403, 'Akun Anda dibekukan oleh administrator.');
        }

        return $next($request);
    }
}
