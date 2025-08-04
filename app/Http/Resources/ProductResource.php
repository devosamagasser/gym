<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'sale' => $this->sale,
            'stock' => $this->stock,
            'is_active' => (bool) $this->is_active,
            'cover_url' => $this->getFirstMediaUrl('cover'),
            'gallery_urls' => $this->getMedia('gallery')->map->getUrl(),
            'category' => $this->whenLoaded('category',function () {
               return new CategoryResource($this->category);
            }),
            'brand' => $this->whenLoaded('brand',function () {
               return new BrandResource($this->brand);
            }),
            'translations' => $this->translations->mapWithKeys(fn($t) => [
                $t->locale => [
                    'name' => $t->name,
                    'description' => $t->description,
                ],
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

