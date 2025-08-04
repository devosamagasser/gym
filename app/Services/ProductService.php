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

    public function list()
    {
        $limit = request()->query('limit', 10);
        return Product::filter(request()->all())->with(['category', 'brand'])->paginate($limit);
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
                $this->addCover($product, $data['cover']);
            }
            $this->addGalery($product, $gallery);

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $translations = Arr::pull($data, 'translations', []);
            $product->update($data);
            $this->fillTranslations($product, $translations);
            $product->save();
            return $product;
        });
    }

    public function updateCover(Product $product, $cover): Product
    {
        return DB::transaction(function () use ($product, $cover) {
            $product->clearMediaCollection('cover');
            $this->addCover($product, $cover);
            return $product;
        });
    }

    public function updateGalery(Product $product, $gallery): Product
    {
        return DB::transaction(function () use ($product, $gallery) {
            $product->clearMediaCollection('gallery');
            $this->addGalery($product, $gallery);
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

    private function addCover($product, $cover)
    {
        $product->addMedia($cover)->toMediaCollection('cover');
    }

    private function addGalery($product, $gallery)
    {
        foreach ($gallery as $image) {
            $product->addMedia($image)->toMediaCollection('gallery');
        }
    }
}

