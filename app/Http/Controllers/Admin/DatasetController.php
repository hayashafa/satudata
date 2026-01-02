<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\YiiApiClient;

// Frontend (tampilan dan routing) menggunakan Laravel.
// Seluruh proses backend dan akses database dikelola penuh oleh sistem Yii.
class DatasetController extends Controller
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

    public function index(Request $request)
    {
        $status = $request->query('status');
        $userId = $request->query('user_id');

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/datasets', [
            'status' => $status,
            'user_id' => $userId,
        ]);

        $rows = [];
        if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
            $rows = $res['data']['data'];
        }

        $datasets = collect($rows)->map(function ($row) {
            return $this->toObject($row);
        });

        $title = match ($status) {
            'pending'  => 'Dataset Menunggu Review',
            'approved' => 'Dataset yang Disetujui',
            default    => 'Daftar Dataset yang Diupload',
        };

        return view('admin.datasets.index', compact('datasets', 'status', 'title'));
    }
    public function create()
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/categories');

        $rows = [];
        if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
            $rows = $res['data']['data'];
        }

        $categories = collect($rows)->map(function ($row) {
            return $this->toObject($row);
        });
        return view('admin.datasets.create', compact('categories'));
    }

    // Daftar dataset yang sudah di-approve (hanya untuk dibaca)
    public function approvedIndex()
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/datasets', ['status' => 'approved']);

        $rows = [];
        if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
            $rows = $res['data']['data'];
        }

        $datasets = collect($rows)->map(function ($row) {
            return $this->toObject($row);
        });

        return view('admin.datasets.approved', compact('datasets'));
    }

    /**
     * Menampilkan detail satu dataset di area admin.
     */
    public function show($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/datasets/' . $id);

        if (!($res['success'] ?? false) || !is_array($res['data']['data'] ?? null)) {
            abort(404);
        }

        $dataset = $this->toObject($res['data']['data']);

        return view('admin.datasets.show', compact('dataset'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'category_id' => 'required|integer',
            'year'        => 'required|integer',

            // File dataset: hanya boleh jenis tertentu, max 5MB
            'file'        => 'nullable|file|mimes:pdf,xlsx,csv,txt|max:5120',

            // Gambar: hanya jpg/jpeg/png, max 2MB
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $multipart = [
            ['name' => 'title', 'contents' => (string) $request->input('title')],
            ['name' => 'description', 'contents' => (string) $request->input('description', '')],
            ['name' => 'category_id', 'contents' => (string) $request->input('category_id')],
            ['name' => 'year', 'contents' => (string) $request->input('year')],
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $multipart[] = [
                'name' => 'file',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $multipart[] = [
                'name' => 'image',
                'contents' => fopen($img->getRealPath(), 'r'),
                'filename' => $img->getClientOriginalName(),
            ];
        }

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->postMultipart('/api/admin/datasets', $multipart);

        if (!($res['success'] ?? false)) {
            $error = 'Dataset gagal ditambahkan.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->back()->with('error', $error)->withInput();
        }

        return redirect()->route('admin.datasets.index')->with('success', 'Dataset berhasil ditambahkan.');
    }

    public function edit($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $resDataset = $yii->get('/api/admin/datasets/' . $id);
        if (!($resDataset['success'] ?? false) || !is_array($resDataset['data']['data'] ?? null)) {
            abort(404);
        }

        $dataset = $this->toObject($resDataset['data']['data']);

        $resCategories = $yii->get('/api/categories');
        $rows = [];
        if (($resCategories['success'] ?? false) && is_array($resCategories['data']['data'] ?? null)) {
            $rows = $resCategories['data']['data'];
        }

        $categories = collect($rows)->map(function ($row) {
            return $this->toObject($row);
        });

        return view('admin.datasets.edit', compact('dataset', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'category_id' => 'required|integer',
            'year'        => 'required|integer',
            'file'        => 'nullable|file|mimes:pdf,xlsx,csv,txt|max:5120',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $multipart = [
            ['name' => 'title', 'contents' => (string) $request->input('title')],
            ['name' => 'description', 'contents' => (string) $request->input('description', '')],
            ['name' => 'category_id', 'contents' => (string) $request->input('category_id')],
            ['name' => 'year', 'contents' => (string) $request->input('year')],
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $multipart[] = [
                'name' => 'file',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $multipart[] = [
                'name' => 'image',
                'contents' => fopen($img->getRealPath(), 'r'),
                'filename' => $img->getClientOriginalName(),
            ];
        }

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->postMultipart('/api/admin/datasets/' . $id . '/update', $multipart);

        if (!($res['success'] ?? false)) {
            $error = 'Dataset gagal diperbarui.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->back()->with('error', $error)->withInput();
        }

        return redirect()->route('admin.datasets.index')->with('success', 'Dataset berhasil diperbarui.');
    }

    public function approve($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->postJson('/api/admin/datasets/' . $id . '/approve');

        if (!($res['success'] ?? false)) {
            $error = 'Dataset gagal di-approve.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->back()->with('error', $error);
        }

        return redirect()->back()->with('success', 'Dataset berhasil di-approve.');
    }

    public function destroy($id)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->delete('/api/admin/datasets/' . $id);

        if (!($res['success'] ?? false)) {
            $error = 'Dataset gagal dihapus.';
            if (is_array($res['data'] ?? null) && !empty($res['data']['error'])) {
                $error = (string) $res['data']['error'];
            }

            return redirect()->route('admin.datasets.index')->with('error', $error);
        }

        return redirect()->route('admin.datasets.index')->with('success', 'Dataset berhasil dihapus.');
    }
}
