<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\YiiApiClient;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    protected function toObject($value)
    {
        if (is_array($value)) {
            $obj = new \stdClass();
            foreach ($value as $k => $v) {
                $obj->{$k} = $this->toObject($v);
            }
            return $obj;
        }

        if (is_object($value)) {
            foreach (get_object_vars($value) as $k => $v) {
                $value->{$k} = $this->toObject($v);
            }
            return $value;
        }

        return $value;
    }

    // daftar semua user/admin dengan pencarian & pengurutan
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sort   = $request->query('sort', 'latest'); // latest, name_az, name_za

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/users', [
            'search' => $search,
            'sort' => $sort,
        ]);

        $rows = [];
        if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
            $rows = $res['data']['data'];
        }

        $users = collect($rows)->map(function ($row) {
            return $this->toObject($row);
        });

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search,
            'sort'   => $sort,
        ]);
    }

    // form tambah user/admin (hanya superadmin di-route)
    public function create()
    {
        return view('admin.users.create');
    }

    // simpan user/admin baru via Yii API
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,superadmin',
        ]);

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->postJson('/api/admin/users', [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => $request->input('role', 'admin'),
        ]);

        if (!($res['success'] ?? false)) {
            $error = 'Gagal menambahkan pengguna baru.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return back()->withInput()->with('error', $error);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // DETAIL: 1 user + semua dataset yg dia upload
    public function show($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/users/' . $id);

        if (!($res['success'] ?? false) || !is_array($res['data']['data'] ?? null)) {
            abort(404);
        }

        $payload = $res['data']['data'];
        $user = $this->toObject($payload['user'] ?? []);
        $datasets = collect(is_array($payload['datasets'] ?? null) ? $payload['datasets'] : [])->map(function ($row) {
            return $this->toObject($row);
        });

        return view('admin.users.show', compact('user', 'datasets'));
    }

    // hapus user
    public function destroy($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->delete('/api/admin/users/' . $id);

        if (!($res['success'] ?? false)) {
            $error = 'Tidak dapat menghapus pengguna ini.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->route('admin.users.index')->with('error', $error);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    // bekukan user (blokir akses admin/upload)
    public function freeze($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->patchJson('/api/admin/users/' . $id . '/freeze');

        if (!($res['success'] ?? false)) {
            $error = 'Gagal membekukan user.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->route('admin.users.index')->with('error', $error);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibekukan.');
    }

    // aktifkan kembali user yang dibekukan
    public function unfreeze($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->patchJson('/api/admin/users/' . $id . '/unfreeze');

        if (!($res['success'] ?? false)) {
            $error = 'Gagal mengubah status user.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->route('admin.users.index')->with('error', $error);
        }

        return redirect()->route('admin.users.index')->with('success', 'Status user berhasil diubah menjadi aktif.');
    }
}