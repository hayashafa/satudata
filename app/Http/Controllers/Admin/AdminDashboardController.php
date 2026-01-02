<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\YiiApiClient;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
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
        $yiiUser = session('yii_user');
        $role = is_array($yiiUser) ? ($yiiUser['role'] ?? null) : null;

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/dashboard-summary');

        $payload = is_array($res['data'] ?? null) ? $res['data'] : [];

        $totalDatasets = (int) ($payload['totalDatasets'] ?? 0);
        $incomingDatasets = (int) ($payload['incomingDatasets'] ?? 0);
        $approvedDatasets = (int) ($payload['approvedDatasets'] ?? 0);
        $totalUsers = (int) ($payload['totalUsers'] ?? 0);

        if ($role === 'superadmin') {
            $latestDatasetsRaw = is_array($payload['latestDatasets'] ?? null) ? $payload['latestDatasets'] : [];
            $topUploadersRaw = is_array($payload['topUploaders'] ?? null) ? $payload['topUploaders'] : [];

            $latestDatasets = collect($latestDatasetsRaw)->map(function ($row) {
                return $this->toObject($row);
            });

            $topUploaders = collect($topUploadersRaw)->map(function ($row) {
                return $this->toObject($row);
            });

            return view('admin.dashboard_superadmin', compact(
                'totalDatasets',
                'incomingDatasets',
                'approvedDatasets',
                'totalUsers',
                'latestDatasets',
                'topUploaders'
            ));
        }

        return view('admin.dashboard_admin', compact('totalDatasets', 'approvedDatasets'));
    }

    public function apiSummary()
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/dashboard-summary');

        $payload = is_array($res['data'] ?? null) ? $res['data'] : [];

        return response()->json([
            'totalDatasets'    => (int) ($payload['totalDatasets'] ?? 0),
            'incomingDatasets' => (int) ($payload['incomingDatasets'] ?? 0),
            'approvedDatasets' => (int) ($payload['approvedDatasets'] ?? 0),
            'totalUsers'       => (int) ($payload['totalUsers'] ?? 0),
        ], ($res['status'] ?? 200));
    }

    /**
     * Halaman detail Rekapan User (khusus superadmin).
     */
    public function rekapanUser()
    {
        $yiiUser = session('yii_user');
        $role = is_array($yiiUser) ? ($yiiUser['role'] ?? null) : null;
        abort_unless($role === 'superadmin', 403);

        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/admin/rekapan-user');

        $rows = [];
        if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
            $rows = $res['data']['data'];
        }

        $topUploaders = collect($rows)->map(function ($row) {
            return $this->toObject($row);
        });

        return view('admin.rekapan_user', compact('topUploaders'));
    }
}
