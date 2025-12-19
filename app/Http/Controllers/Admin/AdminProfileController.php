<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'workplace' => ['nullable', 'string', 'max:255'],
            'gender'    => ['nullable', 'string', 'max:10'],
        ]);

        $user->update($data);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
