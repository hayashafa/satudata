<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\YiiApiClient;
use Illuminate\Http\Request;

class CategoryController extends Controller
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

    public function index()
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/categories');

        $rows = [];
        if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
            $rows = $res['data']['data'];
        }

        $categories = collect($rows)->map(function ($row) {
            return $this->toObject($row);
        });

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->postJson('/api/admin/categories', $validated);

        if (!($res['success'] ?? false)) {
            $error = 'Gagal menambahkan kategori.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->route('admin.categories.index')->with('error', $error);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->delete('/api/admin/categories/' . $id);

        if (!($res['success'] ?? false)) {
            $error = 'Kategori tidak dapat dihapus.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->route('admin.categories.index')->with('error', $error);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
