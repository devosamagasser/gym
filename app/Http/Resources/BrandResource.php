<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
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
                    'description' => $t->description,
                ],
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
