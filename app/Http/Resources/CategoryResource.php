<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translate(app()->getLocale())->name,
            'description' => $this->translate(app()->getLocale())->description,
            'is_active' => $this->is_active,
            'translations' => $this->translations,
        ];
    }
}
