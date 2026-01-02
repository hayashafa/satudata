<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthCustom
{
    public function handle($request, Closure $next)
    {
        // Backend auth sudah di Yii: Laravel hanya menyimpan token + user info di session.
        if (! session('yii_api_token') || ! session('yii_user')) {
            return redirect()->route('login');
        }

        $yiiUser = session('yii_user');

        // Jika akun dibekukan, blokir akses ke area yang memakai middleware ini
        if (is_array($yiiUser) && !empty($yiiUser['is_frozen'])) {
            abort(403, 'Akun Anda dibekukan oleh administrator.');
        }

        return $next($request);
    }
}
