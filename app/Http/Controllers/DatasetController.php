<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\YiiApiClient;

class DatasetController extends Controller
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

    protected function yii(): YiiApiClient
    {
        return app(YiiApiClient::class);
    }
    /**
     * Halaman daftar dataset publik dengan pencarian sederhana.
     */
    public function publicIndex(Request $request)
    {
        $filters = [
            'q'       => $request->query('q'),
            'type'    => $request->query('type'),
            'year'    => $request->query('year'),
            'creator' => $request->query('creator'),
            'format'  => $request->query('format'),
            'sort'    => $request->query('sort', 'latest'),
        ];

        $resDatasets = $this->yii()->get('/api/datasets', array_filter($filters, fn ($v) => $v !== null));
        $items = [];
        if (($resDatasets['success'] ?? false) && is_array($resDatasets['data']['data'] ?? null)) {
            $items = $resDatasets['data']['data'];
        }

        $datasets = collect($items)->map(function ($row) {
            return $this->toObject($row);
        });

        $resCategories = $this->yii()->get('/api/categories');
        $catItems = [];
        if (($resCategories['success'] ?? false) && is_array($resCategories['data']['data'] ?? null)) {
            $catItems = $resCategories['data']['data'];
        }

        $categories = collect($catItems)->map(function ($row) {
            return $this->toObject($row);
        });

        return view('datasets.public', [
            'datasets'   => $datasets,
            'q'          => $filters['q'],
            'type'       => $filters['type'],
            'year'       => $filters['year'],
            'creator'    => $filters['creator'],
            'format'     => $filters['format'],
            'sort'       => $filters['sort'],
            'categories' => $categories,
        ]);
    }

    /**
     * Halaman detail dataset publik.
     */
    public function showPublic($id)
    {
        $res = $this->yii()->get('/api/datasets/' . $id);
        if (!($res['success'] ?? false) || !is_array($res['data']['data'] ?? null)) {
            abort(404);
        }

        $dataset = $this->toObject($res['data']['data']);

        return view('datasets.detail', compact('dataset'));
    }

    /**
     * Menampilkan file dataset (preview CSV atau file langsung).
     */
    public function viewFile($id)
    {
        $res = $this->yii()->get('/api/datasets/' . $id);
        if (!($res['success'] ?? false) || !is_array($res['data']['data'] ?? null)) {
            abort(404);
        }

        $dataset = $this->toObject($res['data']['data']);

        if (empty($dataset->file_path) || !Storage::disk('public')->exists($dataset->file_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($dataset->file_path);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            $content = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $rows = array_map(function ($line) {
                return str_getcsv($line);
            }, $content);

            return view('datasets.csv_view', [
                'dataset' => $dataset,
                'rows'    => $rows,
            ]);
        }

        if (in_array($extension, ['xls', 'xlsx'])) {
            // Untuk saat ini, arahkan saja ke download; implementasi viewer khusus bisa ditambahkan nanti
            return redirect()->route('datasets.downloadFile', $dataset->id);
        }

        // Default: file biasa, kirim response file langsung
        return response()->file($fullPath);
    }

    /**
     * Download file dataset (hanya untuk dataset yang approved).
     */
    public function downloadFile($id)
    {
        $res = $this->yii()->get('/api/datasets/' . $id);
        if (!($res['success'] ?? false) || !is_array($res['data']['data'] ?? null)) {
            abort(404);
        }

        $dataset = $this->toObject($res['data']['data']);

        if (empty($dataset->file_path) || !Storage::disk('public')->exists($dataset->file_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($dataset->file_path);

        return response()->download($fullPath);
    }
}
