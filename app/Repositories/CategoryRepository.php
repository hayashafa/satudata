<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAllOrdered()
    {
        return Category::orderBy('name')->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function deleteIfUnused(int $id): bool
    {
        $category = Category::findOrFail($id);

        if ($category->datasets()->exists()) {
            return false;
        }

        $category->delete();
        return true;
    }
}
