<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetApiController extends Controller
{
    /**
     * API: daftar dataset publik (JSON) dengan filter kategori dan pencarian teks.
     */
    public function index(Request $request)
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

        if (!empty($type)) {
            $query->where('category_id', $type);
        }

        if (!empty($year)) {
            $query->where('year', $year);
        }

        if (!empty($creator)) {
            $query->where('creator', 'like', "%{$creator}%");
        }

        if (!empty($format)) {
            $format = strtolower($format);
            $query->whereNotNull('file_path')
                ->whereRaw('LOWER(file_path) LIKE ?', ['%.' . $format]);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

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

        $datasets = $query->get();

        return response()->json([
            'data' => $datasets,
        ]);
    }
}
