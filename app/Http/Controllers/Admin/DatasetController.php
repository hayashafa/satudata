<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DatasetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $status  = $request->query('status');
        $userId  = $request->query('user_id');

        // superadmin: bisa melihat semua dataset (hanya milik user yang tidak dibekukan),
        // atau filter per user jika user_id diberikan
        if (auth()->user()->isSuperAdmin()) {
            $query = Dataset::with(['category', 'user'])
                ->whereHas('user', function ($user) {
                    $user->where('is_frozen', false);
                })
                ->latest();

            if (!empty($userId)) {
                $query->where('user_id', $userId);
            }
        } else {
            // admin biasa: hanya lihat dataset miliknya sendiri
            $query = Dataset::with(['category', 'user'])
                ->where('user_id', auth()->id())
                ->whereHas('user', function ($user) {
                    $user->where('is_frozen', false);
                })
                ->latest();
        }

        if ($status === 'pending') {
            $query->where('status', 'pending');
            $title = 'Daftar Dataset yang Masuk (Menunggu Review)';
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
            $title = 'Daftar Dataset yang Sudah Disetujui';
        } else {
            $title = 'Daftar Dataset yang Diupload';
        }

        $datasets = $query->get();

        return view('admin.datasets.index', [
            'datasets' => $datasets,
            'status'   => $status,
            'title'    => $title,
        ]);
    }
    public function create()
    {
        $categories = Category::all();
        return view('admin.datasets.create', compact('categories'));
    }

    // Daftar dataset yang sudah di-approve (hanya untuk dibaca)
    public function approvedIndex()
    {
        $datasets = Dataset::with(['category', 'user'])
            ->where('status', 'approved')
            ->whereHas('user', function ($user) {
                $user->where('is_frozen', false);
            })
            ->latest()
            ->get();

        return view('admin.datasets.approved', compact('datasets'));
    }

    /**
     * Menampilkan detail satu dataset di area admin.
     */
    public function show($id)
    {
        $dataset = Dataset::with(['category', 'user'])->findOrFail($id);

        // Admin biasa hanya boleh melihat dataset miliknya sendiri
        if (!auth()->user()->isSuperAdmin() && $dataset->user_id !== auth()->id()) {
            abort(403);
        }

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

        $data = $request->only(['title', 'description', 'category_id', 'year']);
        $data['user_id']  = Auth::id();
        $data['creator']  = Auth::user()->name ?? 'Admin';
        $data['status']   = 'pending';   // dataset baru = masuk
        if ($request->hasFile('file')) {
            // Simpan ke storage default: storage/app/datasets
            $data['file_path'] = $request->file('file')->store('datasets');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        Dataset::create($data);

        return redirect()->route('admin.datasets.index')
            ->with('success', 'Dataset berhasil ditambahkan.');
    }

    public function edit($id)
   {
    $dataset = Dataset::findOrFail($id);
    $categories = Category::all();

    if (!auth()->user()->isSuperAdmin()) {
        // Admin biasa hanya boleh edit dataset miliknya yg belum approved
        if ($dataset->user_id !== auth()->id() || $dataset->status === 'approved') {
            abort(403); // Forbidden
        }
    }

    return view('admin.datasets.edit', compact('dataset', 'categories'));
}

    public function update(Request $request, $id)
    {
        $dataset = Dataset::findOrFail($id);

        // Admin biasa hanya boleh mengupdate dataset miliknya sendiri yang belum approved
        if (!auth()->user()->isSuperAdmin()) {
            if ($dataset->user_id !== auth()->id() || $dataset->status === 'approved') {
                abort(403);
            }
        }

        $request->validate([
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'category_id' => 'required|integer',
            'year'        => 'required|integer',
            'file'        => 'nullable|file|mimes:pdf,xlsx,csv,txt|max:5120',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title', 'description', 'category_id', 'year']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('file')) {
            // Simpan ke storage default: storage/app/datasets
            $data['file_path'] = $request->file('file')->store('datasets');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $dataset->update($data);

        return redirect()->route('admin.datasets.index')
            ->with('success', 'Dataset berhasil diperbarui.');
    }

    public function approve($id)
    {
        $dataset = Dataset::findOrFail($id);

        // Jika belum pernah disetujui, isi approved_at dengan tanggal saat ini
        $dataUpdate = ['status' => 'approved'];

        if (is_null($dataset->approved_at)) {
            $dataUpdate['approved_at'] = now();
        }

        $dataset->update($dataUpdate);

        return redirect()->back()->with('success', 'Dataset berhasil di-approve.');
    }

    public function destroy($id)
    {
        $dataset = Dataset::findOrFail($id);

        // Admin biasa hanya boleh menghapus dataset miliknya sendiri yang belum approved
        if (!auth()->user()->isSuperAdmin()) {
            if ($dataset->user_id !== auth()->id() || $dataset->status === 'approved') {
                abort(403);
            }
        }

        $dataset->delete();

        return redirect()->route('admin.datasets.index')
            ->with('success', 'Dataset berhasil dihapus.');
    }
}
