<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\YiiApiClient;

class AdminProfileController extends Controller
{
    public function edit()
    {
        $yiiUser = session('yii_user');
        $user = is_array($yiiUser) ? (object) $yiiUser : null;
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255'],
            'workplace' => ['nullable', 'string', 'max:255'],
            'gender'    => ['nullable', 'string', 'max:10'],
        ]);

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->postJson('/api/admin/profile', $data);

        if (!($res['success'] ?? false)) {
            $error = 'Profil gagal diperbarui.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->route('admin.profile.edit')->with('error', $error);
        }

        if (is_array($res['data']['data'] ?? null)) {
            $current = session('yii_user');
            $merged = is_array($current) ? array_merge($current, $res['data']['data']) : $res['data']['data'];
            session(['yii_user' => $merged]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Profil berhasil diperbarui.');
    }
}
