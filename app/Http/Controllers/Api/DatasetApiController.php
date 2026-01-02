<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\YiiApiClient;
use Illuminate\Http\Request;

class DatasetApiController extends Controller
{
    /**
     * API: daftar dataset publik (JSON) dengan filter kategori dan pencarian teks.
     */
    public function index(Request $request)
    {
        /** @var YiiApiClient $yii */
        $yii = app(YiiApiClient::class);
        $res = $yii->get('/api/datasets', $request->query());

        $data = [];
        if (($res['success'] ?? false) && is_array($res['data']['data'] ?? null)) {
            $data = $res['data']['data'];
        }

        return response()->json([
            'data' => $data,
        ], ($res['status'] ?? 200));
    }
}
