<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Arr;
use App\Trait\TranslationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    use TranslationTrait;

    public function list(array $filters = [])
    {
        $limit = request()->query('limit', 10);
        return Category::filter(request()->all())
                    ->withCount('products')
                    ->paginate($limit);
    }

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $translations = Arr::pull($data, 'translations', []);
            $category = Category::create($data);
            $this->fillTranslations($category, $translations);

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
            $this->fillTranslations($category, $translations);
            $category->save();
            return $category;
        });
    }

    public function updateCover(Category $category, $cover): Category
    {
        return DB::transaction(function () use ($category, $cover) {
            $category->clearMediaCollection('cover');
            $category->addMedia($cover)->toMediaCollection('cover');
            return $category;
        });
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }


    public function find(string $id)
    {
        try {
            return Category::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Category not found with ID: {$id}");
        }
    }
}
