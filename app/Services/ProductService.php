<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;
use App\Trait\TranslationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    use TranslationTrait;

    public function list(array $filters = [])
    {
        $limit = request()->query('limit', 10);
        return Product::with(['category', 'brand'])->paginate($limit);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $translations = Arr::pull($data, 'translations', []);
            $gallery = Arr::pull($data, 'gallery', []);

            $product = Product::create($data);
            $this->fillTranslations($product, $translations);
            $product->save();

            if (isset($data['cover'])) {
                $product->addMedia($data['cover'])->toMediaCollection('cover');
            }

            foreach ($gallery as $image) {
                $product->addMedia($image)->toMediaCollection('gallery');
            }

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $translations = Arr::pull($data, 'translations', []);
            $gallery = Arr::pull($data, 'gallery', null);

            if (isset($data['cover'])) {
                $product->clearMediaCollection('cover');
                $product->addMedia($data['cover'])->toMediaCollection('cover');
            }

            if ($gallery !== null) {
                $product->clearMediaCollection('gallery');
                foreach ($gallery as $image) {
                    $product->addMedia($image)->toMediaCollection('gallery');
                }
            }

            $product->update($data);
            $this->fillTranslations($product, $translations);
            $product->save();

            return $product;
        });
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function find(string $id): Product
    {
        try {
            return Product::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Product not found with ID: {$id}");
        }
    }
}

