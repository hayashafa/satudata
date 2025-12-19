<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dataset;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    // daftar semua user/admin dengan pencarian & pengurutan
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sort   = $request->query('sort', 'latest'); // latest, name_az, name_za

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'name_az':
                $query->orderBy('name', 'asc');
                break;
            case 'name_za':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
        }

        $users = $query->get();

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search,
            'sort'   => $sort,
        ]);
    }

    // DETAIL: 1 user + semua dataset yg dia upload
    public function show($id)
    {
        // ambil user
        $user = User::findOrFail($id);

        // ambil semua dataset milik user ini (dengan kategori & user)
        // Jika akun dibekukan, dataset-nya disembunyikan sementara.
        if ($user->isFrozen()) {
            $datasets = collect();
        } else {
            $datasets = Dataset::with(['category', 'user'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        // atau kalau sudah buat relasi di model User:
        // $datasets = $user->datasets()->with('category')->latest()->get();

        return view('admin.users.show', compact('user', 'datasets'));
    }

    // hapus user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // bekukan user (blokir akses admin/upload)
    public function freeze(User $user)
    {
        $user->is_frozen = true;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibekukan.');
    }

    // aktifkan kembali user yang dibekukan
    public function unfreeze(User $user)
    {
        $user->is_frozen = false;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Status user berhasil diubah menjadi aktif.');
    }
}