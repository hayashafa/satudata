<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // jumlah dataset yang diupload
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            // superadmin: lihat semua dataset yang sudah diupload
            $totalDatasets = Dataset::count();
        } else {
            // admin biasa: hanya dataset miliknya sendiri
            $totalDatasets = Dataset::where('user_id', auth()->id())->count();
        }

        // jumlah dataset yang masuk (pending) - global
        $incomingDatasets = Dataset::where('status', 'pending')->count();

        // jumlah dataset yang sudah di-approve (global)
        $approvedDatasets = Dataset::where('status', 'approved')->count();

        $totalUsers  = User::count();

        if (auth()->user() && auth()->user()->isSuperAdmin()) {

            // Daftar ringkas 5 dataset terbaru (untuk ditampilkan di dashboard)
            $latestDatasets = Dataset::with(['category', 'user'])
                ->latest()
                ->limit(5)
                ->get();

            // Statistik  tributor dengan upload terbanyak (semua user, diurutkan)
            $topUploaders = User::withCount([
                    'datasets',
                    'datasets as approved_datasets_count' => function ($q) {
                        $q->where('status', 'approved');
                    },
                    'datasets as pending_datasets_count' => function ($q) {
                        $q->where('status', 'pending');
                    },
                    'datasets as rejected_datasets_count' => function ($q) {
                        $q->where('status', 'rejected');
                    },
                    'datasets as edited_datasets_count' => function ($q) {
                        $q->whereColumn('updated_at', '>', 'created_at');
                    },
                ])
                ->orderByDesc('datasets_count')
                ->get();

            return view('admin.dashboard_superadmin', compact(
                'totalDatasets',
                'incomingDatasets',
                'approvedDatasets',
                'totalUsers',
                'latestDatasets',
                'topUploaders'
            ));
        }

        return view('admin.dashboard_admin', compact(
            'totalDatasets',
            'approvedDatasets'
        ));
    }

    public function apiSummary()
    {
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            $totalDatasets = Dataset::count();
            $incomingDatasets = Dataset::where('status', 'pending')->count();
            $approvedDatasets = Dataset::where('status', 'approved')->count();
        } else {
            $totalDatasets = Dataset::where('user_id', auth()->id())->count();
            $incomingDatasets = Dataset::where('status', 'pending')
                ->where('user_id', auth()->id())
                ->count();
            $approvedDatasets = Dataset::where('status', 'approved')
                ->where('user_id', auth()->id())
                ->count();
        }

        $totalUsers = User::count();

        return response()->json([
            'totalDatasets'    => $totalDatasets,
            'incomingDatasets' => $incomingDatasets,
            'approvedDatasets' => $approvedDatasets,
            'totalUsers'       => $totalUsers,
        ]);
    }

    /**
     * Halaman detail Rekapan User (khusus superadmin).
     */
    public function rekapanUser()
    {
        // Hanya superadmin yang boleh mengakses
        abort_unless(auth()->user() && auth()->user()->isSuperAdmin(), 403);

        $topUploaders = User::withCount([
                'datasets',
                'datasets as approved_datasets_count' => function ($q) {
                    $q->where('status', 'approved');
                },
                'datasets as pending_datasets_count' => function ($q) {
                    $q->where('status', 'pending');
                },
                'datasets as rejected_datasets_count' => function ($q) {
                    $q->where('status', 'rejected');
                },
                'datasets as edited_datasets_count' => function ($q) {
                    $q->whereColumn('updated_at', '>', 'created_at');
                },
            ])
            ->orderByDesc('datasets_count')
            ->get();

        return view('admin.rekapan_user', compact('topUploaders'));
    }
}
