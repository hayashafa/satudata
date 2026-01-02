<?php

namespace App\Repositories;

use App\Models\User;

class AdminUserRepository
{
    public function getAdminUsers(?string $search, string $sort)
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->whereIn('role', ['admin', 'superadmin']);

        $query = match ($sort) {
            'name_az' => $query->orderBy('name', 'asc'),
            'name_za' => $query->orderBy('name', 'desc'),
            default   => $query->orderByDesc('created_at'),
        };

        return $query->get();
    }

    public function findWithDatasets(int $id): User
    {
        return User::with('datasets')->findOrFail($id);
    }

    public function delete(int $id): bool
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id || $user->role === 'superadmin') {
            return false;
        }

        $user->delete();
        return true;
    }

    public function setFrozen(int $id, bool $frozen): void
    {
        $user = User::findOrFail($id);
        $user->is_frozen = $frozen;
        $user->save();
    }
}
