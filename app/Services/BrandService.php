<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Arr;
use App\Trait\TranslationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BrandService
{
    use TranslationTrait;

    public function list(array $filters = [])
    {
        $limit = request()->query('limit', 10);
        return Brand::filter(request()->all())
                    ->withCount('products')
                    ->paginate($limit);
    }

    public function create(array $data): Brand
    {
        return DB::transaction(function () use ($data) {
            $translations = Arr::pull($data, 'translations', []);
            $brand = Brand::create($data);
            $this->fillTranslations($brand, $translations);

            $brand->save();

            if (isset($data['cover'])) {
                $brand->addMedia($data['cover'])->toMediaCollection('cover');
            }
            return $brand;
        });
    }

    public function update(Brand $brand, array $data): Brand
    {
        return DB::transaction(function () use ($brand, $data) {
            $translations = Arr::pull($data, 'translations', []);
            $brand->update($data);
            $this->fillTranslations($brand, $translations);
            $brand->save();
            return $brand;
        });
    }

    public function updateCover(Brand $brand, $cover): Brand
    {
        return DB::transaction(function () use ($brand, $cover) {
            $brand->clearMediaCollection('cover');
            $brand->addMedia($cover)->toMediaCollection('cover');
            return $brand;
        });
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }


    public function find(string $id)
    {
        try {
            return Brand::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Brand not found with ID: {$id}");
        }
    }
}
