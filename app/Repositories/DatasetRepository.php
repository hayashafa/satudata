<?php

namespace App\Repositories;

use App\Models\Dataset;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DatasetRepository
{
    public function getAdminIndexDatasets(?string $status, ?int $userId, ?User $currentUser)
    {
        $query = Dataset::with(['category', 'user'])->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($currentUser && !$currentUser->isSuperAdmin()) {
            $query->where('user_id', $currentUser->id);
        } elseif ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    public function getApprovedAdminDatasets()
    {
        return Dataset::with(['category', 'user'])
            ->where('status', 'approved')
            ->orderByDesc('approved_at')
            ->get();
    }

    public function findAdminDatasetWithRelations(int $id): Dataset
    {
        return Dataset::with(['category', 'user'])->findOrFail($id);
    }

    public function createFromRequest(Request $request): Dataset
    {
        $dataset = new Dataset();
        $dataset->fill($request->only(['title', 'description', 'category_id', 'year']));
        $dataset->user_id = Auth::id();
        $dataset->creator = Auth::user()->name ?? 'Admin';
        $dataset->status  = 'pending';

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('datasets/files', 'public');
            $dataset->file_path = $path;
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('datasets/images', 'public');
            $dataset->image = $imagePath;
        }

        $dataset->save();

        return $dataset;
    }

    public function updateFromRequest(Request $request, int $id): Dataset
    {
        $dataset = Dataset::findOrFail($id);

        if (!Auth::user()->isSuperAdmin() && (Auth::id() !== $dataset->user_id || $dataset->status === 'approved')) {
            abort(403);
        }

        $dataset->fill($request->only(['title', 'description', 'category_id', 'year']));

        if ($request->hasFile('file')) {
            if ($dataset->file_path) {
                Storage::disk('public')->delete($dataset->file_path);
            }
            $path = $request->file('file')->store('datasets/files', 'public');
            $dataset->file_path = $path;
        }

        if ($request->hasFile('image')) {
            if ($dataset->image) {
                Storage::disk('public')->delete($dataset->image);
            }
            $imagePath = $request->file('image')->store('datasets/images', 'public');
            $dataset->image = $imagePath;
        }

        $dataset->save();

        return $dataset;
    }

    public function approve(int $id): Dataset
    {
        $dataset = Dataset::find($id);

        if (!$dataset) {
            abort(404);
        }

        $dataset->status      = 'approved';
        $dataset->approved_at = now();
        $dataset->save();

        return $dataset;
    }

    public function delete(int $id): void
    {
        $dataset = Dataset::findOrFail($id);

        if (!Auth::user()->isSuperAdmin() && (Auth::id() !== $dataset->user_id || $dataset->status === 'approved')) {
            abort(403);
        }

        if ($dataset->file_path) {
            Storage::disk('public')->delete($dataset->file_path);
        }

        if ($dataset->image) {
            Storage::disk('public')->delete($dataset->image);
        }

        $dataset->delete();
    }

    public function getPublicDatasets(array $filters = [])
    {
        $q       = $filters['q'] ?? null;
        $type    = $filters['type'] ?? null;
        $year    = $filters['year'] ?? null;
        $creator = $filters['creator'] ?? null;
        $format  = $filters['format'] ?? null;
        $sort    = $filters['sort'] ?? 'latest';

        $query = Dataset::with('category')
            ->where('status', 'approved');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($type) {
            $query->where('category_id', $type);
        }

        if ($year) {
            $query->where('year', $year);
        }

        if ($creator) {
            $query->where('creator', 'like', "%{$creator}%");
        }

        if ($format) {
            $query->where('file_path', 'like', "%.{$format}");
        }

        $query = match ($sort) {
            'oldest'   => $query->orderBy('created_at', 'asc'),
            'title_az' => $query->orderBy('title', 'asc'),
            'title_za' => $query->orderBy('title', 'desc'),
            default    => $query->orderByDesc('created_at'),
        };

        return $query->get();
    }

    public function getPublicCategories()
    {
        return Category::orderBy('name')->get();
    }

    public function findApprovedPublicDatasetWithCategory(int $id): Dataset
    {
        return Dataset::with('category')
            ->where('status', 'approved')
            ->findOrFail($id);
    }

    public function findApprovedDatasetWithFile(int $id): Dataset
    {
        $dataset = Dataset::where('status', 'approved')->findOrFail($id);

        if (!$dataset->file_path || !Storage::disk('public')->exists($dataset->file_path)) {
            abort(404);
        }

        return $dataset;
    }
}
