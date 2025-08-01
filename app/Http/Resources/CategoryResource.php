<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'cover_url' => $this->getFirstMediaUrl('cover'),
            'products_count' => $this->products_count ?? $this->products()->count(),
            'translations' => $this->translations->mapWithKeys(fn($t) => [
                $t->locale => [
                    'name' => $t->name,
                    'description' => $t->description,
                ],
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
