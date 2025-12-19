<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DatasetController extends Controller
{
    /**
     * Halaman daftar dataset publik dengan pencarian sederhana.
     */
    public function publicIndex(Request $request)
    {
        $q       = $request->query('q');
        $type    = $request->query('type');    // id kategori atau null/empty untuk semua
        $year    = $request->query('year');
        $creator = $request->query('creator'); // instansi/OPD pembuat data
        $format  = $request->query('format');  // ekstensi file: pdf, xlsx, csv, txt, dll
        $sort    = $request->query('sort', 'latest'); // latest, oldest, title_az, title_za

        $query = Dataset::with(['category', 'user'])
            ->where('status', 'approved')
            ->whereHas('user', function ($user) {
                $user->where('is_frozen', false);
            });

        // Filter berdasarkan kategori jika dipilih
        if (!empty($type)) {
            $query->where('category_id', $type);
        }

        // Filter tahun rilis (jika tersedia)
        if (!empty($year)) {
            $query->where('year', $year);
        }

        // Filter instansi/OPD berdasarkan kolom creator (jika diisi)
        if (!empty($creator)) {
            $query->where('creator', 'like', "%{$creator}%");
        }

        // Filter format file berdasarkan ekstensi di file_path
        if (!empty($format)) {
            $format = strtolower($format);
            $query->whereNotNull('file_path')
                ->whereRaw('LOWER(file_path) LIKE ?', ['%.' . $format]);
        }

        // Filter pencarian teks
        if ($q) {
            $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                })
                ->orWhereHas('category', function ($cat) use ($q) {
                    $cat->where('name', 'like', "%{$q}%");
                });
        }

        // Sorting hasil
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title_az':
                $query->orderBy('title', 'asc');
                break;
            case 'title_za':
                $query->orderBy('title', 'desc');
                break;
            default: // latest
                $query->latest();
        }

        $datasets   = $query->get();
        $categories = Category::all();

        return view('datasets.public', [
            'datasets'   => $datasets,
            'q'          => $q,
            'type'       => $type,
            'year'       => $year,
            'creator'    => $creator,
            'format'     => $format,
            'sort'       => $sort,
            'categories' => $categories,
        ]);
    }

    /**
     * Halaman detail dataset publik.
     */
    public function showPublic($id)
    {
        $dataset = Dataset::with(['category', 'user'])
            ->where('status', 'approved')
            ->whereHas('user', function ($user) {
                $user->where('is_frozen', false);
            })
            ->findOrFail($id);

        return view('datasets.detail', [
            'dataset' => $dataset,
        ]);
    }

    /**
     * Menampilkan file dataset (preview CSV atau file langsung).
     */
    public function viewFile($id)
    {
        if (auth()->check()) {
            // Admin / superadmin: boleh melihat semua dataset berdasarkan ID
            $dataset = Dataset::findOrFail($id);
        } else {
            // Pengunjung publik: hanya dataset approved milik user yang tidak dibekukan
            $dataset = Dataset::where('status', 'approved')
                ->whereHas('user', function ($user) {
                    $user->where('is_frozen', false);
                })
                ->findOrFail($id);
        }

        if (!$dataset->file_path) {
            abort(404);
        }

        $relativePath = $dataset->file_path;

        // Cek di lokasi baru (storage/app/...)
        $storageDisk = 'local';

        if (!Storage::exists($relativePath)) {
            // Jika tidak ada, coba cek di lokasi lama (storage/app/public/...)
            if (!Storage::disk('public')->exists($relativePath)) {
                abort(404);
            }

            $storageDisk = 'public';
        }

        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            $content = Storage::disk($storageDisk)->get($relativePath);
            $lines   = preg_split("/(\r\n|\n|\r)/", trim($content));

            $rows = [];
            foreach ($lines as $line) {
                if ($line === '') {
                    continue;
                }
                $rows[] = str_getcsv($line);
            }

            return view('datasets.csv_view', [
                'dataset' => $dataset,
                'rows'    => $rows,
            ]);
        }

        if (in_array($extension, ['xlsx', 'xls'])) {
            if ($storageDisk === 'public') {
                $publicUrl = asset('storage/' . ltrim($relativePath, '/'));
            } else {
                $publicUrl = route('datasets.downloadFile', $dataset->id);
            }

            $fileUrl   = urlencode($publicUrl);
            $viewerUrl = "https://view.officeapps.live.com/op/view.aspx?src={$fileUrl}";

            return view('datasets.excel_view', [
                'dataset'   => $dataset,
                'viewerUrl' => $viewerUrl,
            ]);
        }

        $basePath = $storageDisk === 'public'
            ? storage_path('app/public/')
            : storage_path('app/');

        return response()->file(
            $basePath . $relativePath,
            ['Cache-Control' => 'no-store, max-age=0']
        );
    }

    /**
     * Download file dataset (hanya untuk dataset yang approved).
     */
    public function downloadFile($id)
    {
        if (auth()->check()) {
            // Admin / superadmin: bisa mengunduh semua dataset berdasarkan ID
            $dataset = Dataset::findOrFail($id);
        } else {
            // Pengunjung publik: hanya dataset approved milik user yang tidak dibekukan
            $dataset = Dataset::where('status', 'approved')
                ->whereHas('user', function ($user) {
                    $user->where('is_frozen', false);
                })
                ->findOrFail($id);
        }

        if (!$dataset->file_path) {
            abort(404);
        }

        $relativePath = $dataset->file_path;

        // Lokasi baru (storage/app/...)
        $storageDisk = 'local';

        if (!Storage::exists($relativePath)) {
            // Fallback ke lokasi lama (storage/app/public/...)
            if (!Storage::disk('public')->exists($relativePath)) {
                abort(404);
            }

            $storageDisk = 'public';
        }

        $basePath = $storageDisk === 'public'
            ? storage_path('app/public/')
            : storage_path('app/');

        return response()->download($basePath . $relativePath);
    }
}
