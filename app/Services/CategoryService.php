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

    public function list($filter = [], $limit = 10)
    {
        $fields = explode(',', request()->query('fields', '*'));

        $query = Category::filter($filter)
                    ->withCount('products');

        $allowedFields = ['id', 'is_active', 'created_at', 'updated_at']; 
        $selectedFields = array_intersect($fields, $allowedFields);

        if (!empty($selectedFields)) {
            $query->select($selectedFields);
        }

        return $query->paginate($limit);
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


    public function find(string $id, $isActive = false): Category
    {
        try {
            return Category::when($isActive, function($q){
                return $q->where('is_active', true);
            })
            ->where('id', $id)
            ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Category not found with ID: {$id}");
        }
    }
}
