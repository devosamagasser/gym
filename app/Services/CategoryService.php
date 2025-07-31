<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function list(array $filters = [])
    {
        $query = Category::query();

        if (!empty($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }

        if (isset($filters['with'])) {
            $with = array_map('trim', explode(',', $filters['with']));
            $query->with($with);
        }

        $query->withCount('products');

        return $query->paginate(Arr::get($filters, 'limit', 10));
    }

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $translations = Arr::pull($data, 'translations', []);
            $category = Category::create($data);
            foreach ($translations as $locale => $fields) {
                $category->translateOrNew($locale)->fill($fields);
            }
            $category->save();

            if (isset($data['cover'])) {
                $category->addMedia($data['cover'])->toMediaCollection('cover');
            }
            return $category;
        });
    }

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            $translations = Arr::pull($data, 'translations', []);
            $category->update($data);
            foreach ($translations as $locale => $fields) {
                $category->translateOrNew($locale)->fill($fields);
            }
            $category->save();

            if (isset($data['cover'])) {
                $category->clearMediaCollection('cover');
                $category->addMedia($data['cover'])->toMediaCollection('cover');
            }

            return $category;
        });
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
